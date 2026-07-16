<?php

require_once "core/Models/Model.php";
require_once "core/helpers/vendor/modification_helper.php";

class NINModification extends Model {

    private $helper;
    
    public function __construct() {
        parent::__construct();
        $this->helper = new ModificationHelper();
    }

    public function createNINModificationRequest($userId, $body, $fee, $documents = []) {
        $ref = "NINMOD_" . time() . rand(1000, 9999);
        $date = date("Y-m-d H:i:s");
        
        $this->createModificationRecord($userId, $ref, $body, $fee, $documents, $date);
        
        $user = $this->getUserById($userId);
        $newBalance = ($user->sWallet ?? 0) - $fee;
        $this->debitUserBeforeTransaction($userId, $newBalance, "NIN Modification Fee", $ref);
        
        return [
            'status' => "success",
            'ref' => $ref,
            'fee' => $fee,
            'new_balance' => $newBalance,
        ];
    }

    public function createNINVerificationRequest($userId, $body, $fee) {
        $ref = "NINVERIF_" . time() . rand(1000, 9999);
        $date = date("Y-m-d H:i:s");
        
        $this->createModificationRecord($userId, $ref, $body, $fee, [], $date);
        
        $user = $this->getUserById($userId);
        $newBalance = ($user->sWallet ?? 0) - $fee;
        $this->debitUserBeforeTransaction($userId, $newBalance, "NIN Verification Fee", $ref);
        
        return [
            'status' => "success",
            'ref' => $ref,
            'fee' => $fee,
            'new_balance' => $newBalance,
        ];
    }

    private function createModificationRecord($userId, $ref, $body, $fee, $documents, $date) {
        $type = $body->modification_type ?? $body->verification_type;
        $sql = "INSERT INTO nin_requests (sId,ref,type,new_value,reason,fee,status,date_created,documents) VALUES (:user,:ref,:type,:value,:reason,:fee,'pending',:date,:docs)";
        $query = $this->connect()->prepare($sql);
        $docData = json_encode($documents);
        $query->execute([
            ':user' => $userId,
            ':ref' => $ref,
            ':type' => $type,
            ':value' => $body->new_value ?? '',
            ':reason' => $body->reason ?? '',
            ':fee' => $fee,
            ':date' => $date,
            ':docs' => $docData
        ]);
        return $this->connect()->lastInsertId();
    }

    public function getModificationFee($type) {
        $settings = $this->getSiteSettings();
        $fees = [
            'name' => $settings->fee_name_mod ?? 5000,
            'phone' => $settings->fee_phone_mod ?? 5000,
            'address' => $settings->fee_address_mod ?? 4000,
            'email' => $settings->fee_email_mod ?? 4000,
            'dob' => $settings->fee_dob_mod ?? 28574,
            'lga' => $settings->fee_lga_mod ?? 3000,
            'gender' => $settings->fee_gender_mod ?? 8000,
            'marital_status' => $settings->fee_marital_mod ?? 6000,
            'nin_verification' => $settings->fee_nin_verification ?? 1000,
            'affidavit' => $settings->fee_affidavit ?? 5000,
            'birth_certificate' => $settings->fee_birth_certificate ?? 10000,
        ];
        return (float)($fees[$type] ?? 5000);
    }

    public function getAllModificationRequests($controller) {
        $controller->requireAdmin();
        
        $limit = 1000;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT nm.*, s.sFname, s.sLname FROM nin_requests nm 
                JOIN subscribers s ON nm.sId = s.sId ORDER BY nm.date_created DESC LIMIT :limit OFFSET :offset";
        
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->execute();
        
        $requests = $query->fetchAll(PDO::FETCH_OBJ);
        $countSql = "SELECT COUNT(*) as count FROM nin_requests";
        $countQuery = $this->connect()->query($countSql);
        $countResult = $countQuery->fetch(PDO::FETCH_ASSOC);
        
        foreach ($requests as $request) {
            $request->documents = json_decode($request->documents, true) ?: [];
            $request->fee_formatted = "N" . number_format($request->fee, 2);
        }
        
        return [
            'requests' => $requests,
            'total_count' => $countResult['count'],
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($countResult['count'] / $limit)
        ];
    }

    public function updateRequestStatus($ref, $status, $adminId, $notes = '', $resultDocument = null) {
        $sql = "UPDATE nin_requests SET status = :status, reviewed_by = :admin, date_reviewed = :date, admin_notes = :notes";
        if ($resultDocument) {
            $sql .= ", result_document = :doc";
        }
        $sql .= " WHERE ref = :ref";
        
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':status', $status, PDO::PARAM_STR);
        $query->bindValue(':admin', $adminId, PDO::PARAM_INT);
        $query->bindValue(':date', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $query->bindValue(':notes', $notes, PDO::PARAM_STR);
        $query->bindValue(':ref', $ref, PDO::PARAM_STR);
        if ($resultDocument) {
            $query->bindValue(':doc', $resultDocument, PDO::PARAM_STR);
        }
        
        $query->execute();
        
        $this->createSystemNotification("Request $ref updated to status: $status");
        return true;
    }

    public function completeModification($ref, $resultDocument) {
        $sql = "SELECT * FROM nin_requests WHERE ref = :ref";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':ref', $ref, PDO::PARAM_STR);
        $query->execute();
        $mod = $query->fetch(PDO::FETCH_OBJ);
        
        if (!$mod || $mod->status !== 'approved') return false;
        
        $user = $this->getUserById($mod->sId);
        $updateField = $this->getUserUpdateField($mod->type);
        if ($updateField && $mod->new_value) {
            $this->updateUserField($mod->sId, $updateField, $mod->new_value);
        }
        
        $this->createUserNotification($mod->sId, "Your NIN modification request ($ref) has been approved and completed.", "/profile");
    }

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
            'nin_verification' => 'sNinVerified',
            'affidavit' => 'sDoc',
            'birth_certificate' => 'sDoc',
        ];
        return $fields[$type] ?? null;
    }

    private function getSiteSettings() {
        $sql = "SELECT * FROM sitesettings WHERE sId = 1";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    private function getUserById($userId) {
        $sql = "SELECT * FROM subscribers WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    private function updateUserField($userId, $field, $value) {
        $sql = "UPDATE subscribers SET $field = :value WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':value', $value, PDO::PARAM_STR);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
    }

    private function createSystemNotification($message) {
        $adminId = $_SESSION['sysId'] ?? 1;
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO notifications (msg, url, reference, created_by, date_created) VALUES (:msg, :url, :ref, :by, :date)";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':msg', $message, PDO::PARAM_STR);
        $query->bindValue(':url', '/admin/ni-requests', PDO::PARAM_STR);
        $query->bindValue(':ref', 'NIN_REQUEST', PDO::PARAM_STR);
        $query->bindValue(':by', $adminId, PDO::PARAM_INT);
        $query->bindValue(':date', $date, PDO::PARAM_STR);
        $query->execute();
    }

    private function createUserNotification($userId, $message, $url) {
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO user_notifications (sId, msg, url, is_read, date_sent) VALUES (:user, :msg, :url, 0, :date)";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':user', $userId, PDO::PARAM_INT);
        $query->bindValue(':msg', $message, PDO::PARAM_STR);
        $query->bindValue(':url', $url, PDO::PARAM_STR);
        $query->bindValue(':date', $date, PDO::PARAM_STR);
        $query->execute();
    }

    public function debitUserBeforeTransaction($userId, $newBalance, $description, $ref) {
        $sql = "UPDATE subscribers SET sWallet = :balance WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':balance', $newBalance, PDO::PARAM_STR);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
    }

    public function creditUserWallet($userId, $amount, $description) {
        $user = $this->getUserById($userId);
        $newBalance = (float)$user->sWallet + (float)$amount;
        $sql = "UPDATE subscribers SET sWallet = :balance WHERE sId = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':balance', $newBalance, PDO::PARAM_STR);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        $this->recordTransaction($userId, $description, $amount, (float)$user->sWallet, $newBalance, 0);
    }

    public function recordTransaction($userId, $description, $amount, $oldBalance, $newBalance, $status) {
        $ref = "TXN_" . time() . rand(1000, 9999);
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO transactions (sId, transref, servicename, amount, oldbal, newbal, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $this->connect()->prepare($sql);
        $query->execute([
            $userId, $ref, $description, $amount, $oldBalance, $newBalance, $status, $date
        ]);
    }

    public function getAllModificationTypes() {
        return [
            ['type' => 'name', 'display' => 'Full Name', 'category' => 'demographic', 'fee' => 5000, 'processing_days' => 3],
            ['type' => 'phone', 'display' => 'Phone Number', 'category' => 'contact', 'fee' => 5000, 'processing_days' => 1],
            ['type' => 'address', 'display' => 'Address', 'category' => 'address', 'fee' => 4000, 'processing_days' => 1],
            ['type' => 'email', 'display' => 'Email Address', 'category' => 'contact', 'fee' => 4000, 'processing_days' => 1],
            ['type' => 'dob', 'display' => 'Date of Birth', 'category' => 'demographic', 'fee' => 28574, 'processing_days' => 5],
            ['type' => 'lga', 'display' => 'Local Govt. Area', 'category' => 'address', 'fee' => 3000, 'processing_days' => 1],
            ['type' => 'gender', 'display' => 'Gender', 'category' => 'demographic', 'fee' => 8000, 'processing_days' => 4],
            ['type' => 'marital_status', 'display' => 'Marital Status', 'category' => 'demographic', 'fee' => 6000, 'processing_days' => 2],
            ['type' => 'nin_verification', 'display' => 'NIN Verification', 'category' => 'verification', 'fee' => 1000, 'processing_days' => 1],
            ['type' => 'affidavit', 'display' => 'Court Affidavit', 'category' => 'document', 'fee' => 5000, 'processing_days' => 2],
            ['type' => 'birth_certificate', 'display' => 'Birth Certificate / Attestation', 'category' => 'document', 'fee' => 10000, 'processing_days' => 3],
        ];
    }
}

