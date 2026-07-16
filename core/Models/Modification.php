<?php

require_once __DIR__ . "/Model.php";

class Modification extends Model {
    
    public $helper;
    
    public function __construct() {
        parent::__construct();
        $this->helper = new ModificationHelper();
    }
    
    // Get all modification fees configured by admin
    public function getModificationFees() {
        $settings = $this->getSiteSettings();
        $fees = [];
        
        // Get all admin configurable modification fees
        $fees['name'] = (float)($settings->fee_name_mod ?? 5000);
        $fees['phone'] = (float)($settings->fee_phone_mod ?? 5000);
        $fees['address'] = (float)($settings->fee_address_mod ?? 4000);
        $fees['email'] = (float)($settings->fee_email_mod ?? 4000);
        $fees['dob'] = (float)($settings->fee_dob_mod ?? 28574);
        $fees['lga'] = (float)($settings->fee_lga_mod ?? 3000);
        $fees['gender'] = (float)($settings->fee_gender_mod ?? 8000);
        $fees['marital_status'] = (float)($settings->fee_marital_mod ?? 6000);
        $fees['affidavit'] = (float)($settings->fee_affidavit ?? 5000);
        $fees['birth_certificate'] = (float)($settings->fee_birth_certificate ?? 10000);
        
        return $fees;
    }
    
    // Update modification fees
    public function updateModificationFee($type, $amount) {
        $amount = (float) $amount;
        $settings = $this->getSiteSettings();
        
        // Update the settings based on modification type
        switch ($type) {
            case 'name':
                $settings->fee_name_mod = $amount;
                break;
            case 'phone':
                $settings->fee_phone_mod = $amount;
                break;
            case 'address':
                $settings->fee_address_mod = $amount;
                break;
            case 'email':
                $settings->fee_email_mod = $amount;
                break;
            case 'dob':
                $settings->fee_dob_mod = $amount;
                break;
            case 'lga':
                $settings->fee_lga_mod = $amount;
                break;
            case 'gender':
                $settings->fee_gender_mod = $amount;
                break;
            case 'marital_status':
                $settings->fee_marital_mod = $amount;
                break;
            case 'affidavit':
                $settings->fee_affidavit = $amount;
                break;
            case 'birth_certificate':
                $settings->fee_birth_certificate = $amount;
                break;
            default:
                return false;
        }
        
        // Save the updated settings
        $sql = "UPDATE sitesettings SET fee_name_mod = :fn, fee_phone_mod = :fp, fee_address_mod = :fa, fee_email_mod = :fe, fee_dob_mod = :fd, fee_lga_mod = :fl, fee_gender_mod = :fg, fee_marital_mod = :fm, fee_affidavit = :fa2, fee_birth_certificate = :fbc WHERE sId = 1";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':fn', $settings->fee_name_mod, PDO::PARAM_STR);
        $query->bindValue(':fp', $settings->fee_phone_mod, PDO::PARAM_STR);
        $query->bindValue(':fa', $settings->fee_address_mod, PDO::PARAM_STR);
        $query->bindValue(':fe', $settings->fee_email_mod, PDO::PARAM_STR);
        $query->bindValue(':fd', $settings->fee_dob_mod, PDO::PARAM_STR);
        $query->bindValue(':fl', $settings->fee_lga_mod, PDO::PARAM_STR);
        $query->bindValue(':fg', $settings->fee_gender_mod, PDO::PARAM_STR);
        $query->bindValue(':fm', $settings->fee_marital_mod, PDO::PARAM_STR);
        $query->bindValue(':fa2', $settings->fee_affidavit, PDO::PARAM_STR);
        $query->bindValue(':fbc', $settings->fee_birth_certificate, PDO::PARAM_STR);
        $query->execute();
        
        return true;
    }
    
    // Record a new modification request
    public function createModificationRequest($userId, $modificationType, $data, $fee, $documents = []) {
        $ref = "NINMOD_" . time() . rand(1000, 9999);
        $date = date("Y-m-d H:i:s");
        
        $sql = "INSERT INTO nin_modifications (sId,ref,type,new_value,reason,fee,status,date_created,documents) VALUES (:user,:ref,:type,:value,:reason,:fee,'pending',:date,:docs)";
        $query = $this->connect()->prepare($sql);
        
        // Prepare document data for JSON storage
        $docData = [];
        foreach ($documents as $doc) {
            $docData[] = [
                'type' => $doc['type'],
                'path' => $doc['path'],
                'verified' => $doc['verified'] ?? false
            ];
        }
        
        $query->bindValue(':user', $userId, PDO::PARAM_INT);
        $query->bindValue(':ref', $ref, PDO::PARAM_STR);
        $query->bindValue(':type', $modificationType, PDO::PARAM_STR);
        $query->bindValue(':value', $data['new_value'] ?? '', PDO::PARAM_STR);
        $query->bindValue(':reason', $data['reason'] ?? '', PDO::PARAM_STR);
        $query->bindValue(':fee', $fee, PDO::PARAM_STR);
        $query->bindValue(':date', $date, PDO::PARAM_STR);
        $query->bindValue(':docs', json_encode($docData), PDO::PARAM_STR);
        $query->execute();
        
        $modificationId = $this->connect()->lastInsertId();
        
        // Create system notification for admin
        $this->createNotification($modificationId, "New NIN Modification Request - Type: $modificationType");
        
        return ['ref' => $ref, 'id' => $modificationId];
    }
    
    // Get all modification requests with pagination
    public function getModificationRequests($limit, $offset = 0, $status = 'all') {
        $sql = "SELECT nm.*, s.sFname, s.sLname, s.sPhone FROM nin_modifications nm 
                 JOIN subscribers s ON nm.sId = s.sId ";
        
        $where = "";
        if ($status !== 'all') {
            $where = " WHERE nm.status = :status ";
        }
        
        $sql .= $where . " ORDER BY nm.date_created DESC LIMIT $limit, $offset";
        
        $query = $this->connect()->prepare($sql);
        if ($status !== 'all') {
            $query->bindValue(':status', $status, PDO::PARAM_STR);
        }
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Get modification request by reference
    public function getModificationByRef($ref) {
        $sql = "SELECT nm.*, s.sFname, s.sLname, s.sPhone FROM nin_modifications nm 
                 JOIN subscribers s ON nm.sId = s.sId WHERE nm.ref = :ref";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':ref', $ref, PDO::PARAM_STR);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    // Update modification status
    public function updateModificationStatus($ref, $status, $adminId, $notes = '', $resultDocument = null) {
        $sql = "UPDATE nin_modifications SET status = :status, reviewed_by = :admin, 
                 date_reviewed = :date, admin_notes = :notes";
        
        // Add result document path if provided
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
        
        // Create system notification
        $this->createNotification($ref, "Modification Request Updated - Status: $status");
        
        return true;
    }
    
    // Get count of modification requests for pagination
    public function getModificationRequestCount($status = 'all') {
        $sql = "SELECT COUNT(*) as count FROM nin_modifications";
        
        if ($status !== 'all') {
            $sql .= " WHERE status = :status";
            $query = $this->connect()->prepare($sql);
            $query->bindValue(':status', $status, PDO::PARAM_STR);
        } else {
            $query = $this->connect()->query($sql);
        }
        
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        
        return $result->count;
    }
    
    // Create system notification
    private function createNotification($entityId, $message) {
        $sql = "INSERT INTO notifications (msg,url,created_at,reference) VALUES (:msg,:url,:date,:ref)";
        $query = $this->connect()->prepare($sql);
        $query->bindValue(':msg', $message, PDO::PARAM_STR);
        $query->bindValue(':url', "/admin/ni-modifications?action=view&id=" . $entityId, PDO::PARAM_STR);
        $query->bindValue(':date', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $query->bindValue(':ref', $entityId, PDO::PARAM_STR);
        $query->execute();
        
        // Notify all admin users
        $adminSql = "SELECT sId FROM systemusers WHERE role = 'admin' OR role = 'super_admin'";
        $adminQuery = $this->connect()->query($adminSql);
        $adminQuery->execute();
        $admins = $adminQuery->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($admins as $admin) {
            $notifSql = "INSERT INTO admin_notifications (admin_id,notification_id,is_read) VALUES (:admin,:notif,0)";
            $notifQuery = $this->connect()->prepare($notifSql);
            $notifQuery->bindValue(':admin', $admin->sId, PDO::PARAM_INT);
            $notifQuery->bindValue(':notif', $this->connect()->lastInsertId(), PDO::PARAM_INT);
            $notifQuery->execute();
        }
    }
    
    // Get all modification types and their metadata
    public function getAllModificationTypes() {
        $types = [
            ['type' => 'name', 'category' => 'demographic', 'status' => 'restricted',
             'requires_court' => true, 'requires_publication' => true],
            ['type' => 'phone', 'category' => 'contact', 'status' => 'standard',
             'requires_police' => true],
            ['type' => 'address', 'category' => 'address', 'status' => 'standard',
             'requires_utility' => true],
            ['type' => 'email', 'category' => 'contact', 'status' => 'standard',
             'requires_id' => true],
            ['type' => 'dob', 'category' => 'demographic', 'status' => 'restricted',
             'requires_birth_cert' => true, 'requires_attestation' => true],
            ['type' => 'lga', 'category' => 'address', 'status' => 'standard',
             'requires_community' => true],
            ['type' => 'gender', 'category' => 'demographic', 'status' => 'restricted',
             'requires_court' => true],
            ['type' => 'marital_status', 'category' => 'demographic', 'status' => 'standard',
             'requires_marriage_cert' => true],
            ['type' => 'affidavit', 'category' => 'document', 'status' => 'standard',
             'requires_court' => true],
            ['type' => 'birth_certificate', 'category' => 'document', 'status' => 'standard',
             'requires_birth_cert' => true],
        ];
        
        return $types;
    }
    
    // Get document requirements for each modification type
    public function getDocumentRequirements($type) {
        $requirements = [
            'name' => [
                'description' => 'Sworn affidavit for name change',
                'documents' => ['court_affidavit'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'phone' => [
                'description' => 'Police report for phone number change',
                'documents' => ['police_report'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'address' => [
                'description' => 'Proof of new residence (utility bill)',
                'documents' => ['utility_bill'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'email' => [
                'description' => 'ID card and email verification',
                'documents' => ['id_card'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'dob' => [
                'description' => 'Birth certificate or attestation',
                'documents' => ['birth_certificate', 'attestation'],
                'min_number' => 2,
                'verification_required' => true,
            ],
            'lga' => [
                'description' => 'Community ID and utility bill',
                'documents' => ['community_id', 'utility_bill'],
                'min_number' => 2,
                'verification_required' => true,
            ],
            'gender' => [
                'description' => 'Court affidavit for gender change',
                'documents' => ['court_affidavit'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'marital_status' => [
                'description' => 'Marriage certificate or divorce decree',
                'documents' => ['marriage_certificate', 'divorce_decree'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'affidavit' => [
                'description' => 'Sworn court affidavit',
                'documents' => ['court_affidavit'],
                'min_number' => 1,
                'verification_required' => true,
            ],
            'birth_certificate' => [
                'description' => 'Birth certificate or attestation from local government',
                'documents' => ['birth_certificate', 'attestation'],
                'min_number' => 1,
                'verification_required' => true,
            ],
        ];
        
        return $requirements[$type] ?? null;
    }
}

