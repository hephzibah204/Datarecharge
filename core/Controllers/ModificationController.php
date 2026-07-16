<?= PHP ?>
<?php

require_once "core/Models/Modification.php";

class ModificationController extends Model {
    
    public $model;
    
    public function __construct() {
        parent::__construct();
        $this->model = new Modification();
    }
    
    // Get site settings
    private function getSiteSettings() {
        $sql = "SELECT * FROM sitesettings WHERE sId = 1";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    // Get all modification fees (admin view)
    public function getAllModificationFees() {
        $settings = $this->getSiteSettings();
        $fees = $this->model->getModificationFees($settings);
        return $fees;
    }
    
    // Update modification fee
    public function updateModificationFee($type, $amount) {
        return $this->model->updateModificationFee($type, $amount);
    }
    
    // Create new modification request (user facing)
    public function createModificationRequest($userId, $body, $documents = []) {
        // Validate modification type
        $modificationTypes = $this->model->getAllModificationTypes();
        $validTypes = array_column($modificationTypes, 'type');
        
        if (!in_array($body['modification_type'], $validTypes)) {
            return ['status' => 'fail', 'msg' => 'Invalid modification type specified'];
        }
        
        // Get fee for this modification
        $settings = $this->getSiteSettings();
        $fees = $this->model->getModificationFees($settings);
        $fee = $fees[$body['modification_type']];
        
        // Check if user has sufficient balance
        $user = $this->getUserDetails($body['user_id'] ?? $userId);
        if ((float)$user->sWallet < (float)$fee) {
            return ['status' => 'fail', 'msg' => 'Insufficient wallet balance'];
        }
        
        // Create the modification request
        $result = $this->model->createModificationRequest($userId, $body['modification_type'], 
            ['new_value' => $body['new_value'], 'reason' => $body['reason']], $fee, $documents);
        
        // Deduct fee from user wallet
        $newBalance = (float)$user->sWallet - (float)$fee;
        $this->debitUser($userId, $newBalance, "NIN Modification Fee - " . $body['modification_type']);
        
        return ['status' => 'success', 'ref' => $result['ref'], 'id' => $result['id']];
    }
    
    // Admin: Get all modification requests
    public function getAllModificationRequests($limit = 1000) {
        $offset = isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0;
        $status = $_GET['status'] ?? 'all';
        
        $requests = $this->model->getModificationRequests($limit, $offset, $status);
        $count = $this->model->getModificationRequestCount($status);
        
        // Add document details for each request
        foreach ($requests as $request) {
            $request->documents = json_decode($request->documents, true) ?: [];
            $request->fee_required = (float)$request->fee;
            $request->can_review = $this->canReviewRequest($request);
        }
        
        $total_pages = ceil($count / $limit);
        
        return [
            'requests' => $requests,
            'total_count' => $count,
            'total_pages' => $total_pages,
            'current_page' => ($_GET['page'] ?? 1),
            'statuses' => ['pending', 'in_review', 'approved', 'declined', 'completed']
        ];
    }
    
    // Admin: Get modification request details
    public function getModificationRequestDetails($ref) {
        $request = $this->model->getModificationByRef($ref);
        
        if (!$request) {
            return ['status' => 'fail', 'msg' => 'Modification request not found'];
        }
        
        $request->documents = json_decode($request->documents, true) ?: [];
        $request->fee_required = (float)$request->fee;
        $request->can_review = $this->canReviewRequest($request);
        $request->requirements = $this->model->getDocumentRequirements($request->type);
        
        return ['status' => 'success', 'request' => $request];
    }
    
    // Check if a request can be reviewed by admin
    private function canReviewRequest($request) {
        if ($request->status === 'pending') {
            return true;
        }
        
        if ($request->status === 'in_review' && !empty($request->reviewed_by)) {
            return true;
        }
        
        return false;
    }
    
    // Admin: Update modification status
    public function updateModificationStatus($ref, $data) {
        // Get admin user ID from session
        $adminId = $_SESSION['sysId'] ?? 1;
        
        // Validate status
        $validStatuses = ['pending', 'in_review', 'approved', 'declined', 'completed'];
        if (!in_array($data['status'], $validStatuses)) {
            return ['status' => 'fail', 'msg' => 'Invalid status specified'];
        }
        
        // Update the status
        $result = $this->model->updateModificationStatus($ref, $data['status'], $adminId, 
            $data['admin_notes'] ?? '', $data['result_document'] ?? null);
        
        if ($result) {
            // If approved, complete the modification
            if ($data['status'] === 'approved') {
                $this->completeModification($ref, $data['result_document'] ?? null);
            }
            
            return ['status' => 'success', 'msg' => 'Modification status updated successfully'];
        } else {
            return ['status' => 'fail', 'msg' => 'Failed to update modification status'];
        }
    }
    
    // Complete approved modification and update user's data
    private function completeModification($ref, $resultDocument = null) {
        $request = $this->model->getModificationByRef($ref);
        
        if (!$request || $request->status !== 'approved') {
            return false;
        }
        
        // Get user details
        $user = $this->getUserDetails($request->sId);
        
        // Update user data based on modification type
        $updateField = $this->getUserUpdateField($request->type);
        $updateValue = $request->new_value;
        
        if ($updateField && $updateValue) {
            $this->updateUserField($request->sId, $updateField, $updateValue);
        }
        
        // Create notification for user
        $this->createUserNotification($request->sId, "Your NIN modification request has been approved", 
            "/profile?tab=modifications");
    }
    
    // Get the database field name for a modification type
    private function getUserUpdateField($type) {
        $fields = [
            'name' => 'sName',
            'phone' => 'sPhone',
            'address' => 'sAddress',
            'email' => 'sEmail',
            'dob' => 'sDob',
            'lga' => 'sLga',
            'gender' => 'sGender',
            'marital_status' => 'sMaritalStatus',
        ];
        
        return $fields[$type] ?? null;
    }
    
    // Update user field
    private function updateUserField($userId, $field, $value) {
        $sql = "UPDATE subscribers SET $field = :value WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':value', $value, PDO::PARAM_STR);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
    }
    
    // Get user details
    private function getUserDetails($userId) {
        $sql = "SELECT * FROM subscribers WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    // Debit user wallet
    private function debitUser($userId, $amount, $description) {
        $user = $this->getUserDetails($userId);
        $newBalance = (float)$user->sWallet - (float)$amount;
        
        $sql = "UPDATE subscribers SET sWallet = :balance WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':balance', $newBalance, PDO::PARAM_STR);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        
        // Record the wallet debit transaction
        $this->recordTransaction($userId, $description, $amount, (float)$user->sWallet, $newBalance, 0);
    }
    
    // Record transaction
    private function recordTransaction($userId, $description, $amount, $oldBalance, $newBalance, $status) {
        $ref = "MOD_" . time() . rand(1000, 9999);
        $date = date("Y-m-d H:i:s");
        
        $sql = "INSERT INTO transactions SET sId = :user, transref = :ref, servicename = :service, 
                 amount = :amount, oldbal = :oldbal, newbal = :newbal, status = :status, date = :date";
        $query = $this->connect()->prepare($sql);
        
        $query->bindValue(':user', $userId, PDO::PARAM_INT);
        $query->bindValue(':ref', $ref, PDO::PARAM_STR);
        $query->bindValue(':service', $description, PDO::PARAM_STR);
        $query->bindValue(':amount', $amount, PDO::PARAM_STR);
        $query->bindValue(':oldbal', $oldBalance, PDO::PARAM_STR);
        $query->bindValue(':newbal', $newBalance, PDO::PARAM_STR);
        $query->bindValue(':status', $status, PDO::PARAM_INT);
        $query->bindValue(':date', $date, PDO::PARAM_STR);
        $query->execute();
    }
    
    // Create user notification
    private function createUserNotification($userId, $message, $url) {
        $date = date("Y-m-d H:i:s");
        
        $sql = "INSERT INTO user_notifications SET sId = :user, msg = :msg, url = :url, is_read = 0, date_sent = :date";
        $query = $this->connect()->prepare($sql);
        
        $query->bindValue(':user', $userId, PDO::PARAM_INT);
        $query->bindValue(':msg', $message, PDO::PARAM_STR);
        $query->bindValue(':url', $url, PDO::PARAM_STR);
        $query->bindValue(':date', $date, PDO::PARAM_STR);
        $query->execute();
    }
    
    // Get admin notifications
    public function getAdminNotifications() {
        $sql = "SELECT an.*, n.msg, n.url, n.created_at FROM admin_notifications an 
                 JOIN notifications n ON an.notification_id = n.notifId 
                 WHERE an.is_read = 0 AND an.admin_id = :admin_id 
                 ORDER BY an.created_at DESC LIMIT 10";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':admin_id', $_SESSION['sysId'], PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Mark admin notification as read
    public function markNotificationAsRead($notificationId) {
        $sql = "UPDATE admin_notifications SET is_read = 1 WHERE id = :id AND admin_id = :admin_id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':id', $notificationId, PDO::PARAM_INT);
        $query->bindValue(':admin_id', $_SESSION['sysId'], PDO::PARAM_INT);
        $query->execute();
        
        return true;
    }
    
    // Get all modification types for user selection
    public function getAllModificationTypesForUser() {
        $types = $this->model->getAllModificationTypes();
        $result = [];
        
        foreach ($types as $type) {
            $requirements = $this->model->getDocumentRequirements($type['type']);
            $settings = $this->getSiteSettings();
            $fees = $this->model->getModificationFees($settings);
            
            $result[] = [
                'type' => $type['type'],
                'display' => $type['display'],
                'category' => $type['category'],
                'fee' => $fees[$type['type']] ?? 0,
                'requirements' => $requirements
            ];
        }
        
        return $result;
    }
}

