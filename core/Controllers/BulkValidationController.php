<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/NINModification.php';

class BulkValidationController extends Controller {
    
    private $ninModel;
    
    public function __construct() {
        parent::__construct();
        $this->ninModel = new NINModification();
    }
    
    public function displayBulkValidationRequests() {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        
        $sql = "SELECT r.*, s.sFname, s.sLname, s.sPhone FROM bulk_nin_validation_requests r LEFT JOIN subscribers s ON r.sId = s.sId WHERE 1=1";
        $params = [];
        
        if (!empty($status)) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }
        if (!empty($type)) {
            $sql .= " AND r.validation_type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY r.id DESC";
        $query = $pdo->prepare($sql);
        $query->execute($params);
        $requests = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($requests)) {
            return '<tr><td colspan="8" class="text-center">No bulk validation requests found.</td></tr>';
        }
        
        $html = '';
        foreach ($requests as $req) {
            $statusBadge = '<span class="label label-' . ($req['status'] == 'completed' ? 'success' : ($req['status'] == 'processing' ? 'info' : ($req['status'] == 'failed' ? 'danger' : 'warning'))) . '">' . ucfirst($req['status']) . '</span>';
            
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($req['ref']) . '</td>';
            $html .= '<td>' . htmlspecialchars($req['sFname'] . ' ' . $req['sLname']) . '<br><small>' . htmlspecialchars($req['sPhone']) . '</small></td>';
            $html .= '<td>' . ucfirst($req['validation_type']) . '</td>';
            $html .= '<td>' . $req['total_nins'] . ' NINs</td>';
            $html .= '<td>₦' . number_format($req['total_amount'], 2) . '</td>';
            $html .= '<td>' . $statusBadge . '</td>';
            $html .= '<td>' . $req['date_created'] . '</td>';
            $html .= '<td>';
            $html .= '<a href="?url=bulk-validation&view=' . $req['id'] . '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View NINs</a> ';
            $html .= '<button class="btn btn-xs btn-success" onclick="updateBatchStatusModal(' . $req['id'] . ', \'' . $req['status'] . '\')"><i class="fa fa-edit"></i> Status</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        return $html;
    }
    
    public function displayBatchDetails($batchId) {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        
        $stmt = $pdo->prepare("SELECT r.*, s.sFname, s.sLname, s.sPhone, s.sEmail FROM bulk_nin_validation_requests r LEFT JOIN subscribers s ON r.sId = s.sId WHERE r.id = ?");
        $stmt->execute([$batchId]);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$batch) {
            return '<div class="alert alert-danger">Batch not found.</div>';
        }
        
        $itemStmt = $pdo->prepare("SELECT * FROM bulk_nin_validation_items WHERE batch_id = ?");
        $itemStmt->execute([$batchId]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $html = '<div class="box">
            <div class="box-header">
                <h3 class="box-title">Batch Details: ' . htmlspecialchars($batch['ref']) . '</h3>
                <div class="box-tools">
                    <a href="?url=bulk-validation" class="btn btn-default btn-sm">Back to List</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Customer:</strong> ' . htmlspecialchars($batch['sFname'] . ' ' . $batch['sLname']) . ' (' . htmlspecialchars($batch['sPhone']) . ')</p>
                        <p><strong>Validation Type:</strong> ' . ucfirst($batch['validation_type']) . '</p>
                        <p><strong>Total NINs:</strong> ' . $batch['total_nins'] . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Amount:</strong> ₦' . number_format($batch['total_amount'], 2) . '</p>
                        <p><strong>Status:</strong> <span class="label label-' . ($batch['status'] == 'completed' ? 'success' : 'warning') . '">' . ucfirst($batch['status']) . '</span></p>
                        <p><strong>Date:</strong> ' . $batch['date_created'] . '</p>
                    </div>
                </div>
                <hr>
                <h4>Submitted NIN Items</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NIN</th>
                            <th>Status</th>
                            <th>Admin Reply / Result</th>
                            <th>Result Document</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($items as $i => $item) {
            $html .= '<tr>';
            $html .= '<td>' . ($i + 1) . '</td>';
            $html .= '<td><strong>' . htmlspecialchars($item['nin']) . '</strong></td>';
            $html .= '<td><span class="label label-' . ($item['status'] == 'completed' ? 'success' : ($item['status'] == 'failed' ? 'danger' : 'warning')) . '">' . ucfirst($item['status']) . '</span></td>';
            $html .= '<td>' . htmlspecialchars($item['admin_reply'] ?? 'Pending review') . '</td>';
            $html .= '<td>' . (!empty($item['result_document']) ? '<a href="' . $item['result_document'] . '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"></i> View Report</a>' : 'None') . '</td>';
            $html .= '<td><button class="btn btn-xs btn-primary" onclick="editItem(' . $item['id'] . ', \'' . $item['nin'] . '\', \'' . $item['status'] . '\', ' . htmlspecialchars(json_encode($item['admin_reply'] ?? '')) . ')">Update Item</button></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></div></div>';
        return $html;
    }
    
    public function updateBatchStatus($data) {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        $batchId = $data['batch_id'] ?? 0;
        $status = $data['status'] ?? 'pending';
        
        $now = date("Y-m-d H:i:s");
        $stmt = $pdo->prepare("UPDATE bulk_nin_validation_requests SET status = ?, date_processed = ? WHERE id = ?");
        $stmt->execute([$status, $now, $batchId]);

        $itemStmt = $pdo->prepare("UPDATE bulk_nin_validation_items SET status = ?, date_updated = ? WHERE batch_id = ?");
        $itemStmt->execute([$status, $now, $batchId]);
        
        return ['status' => 'success', 'msg' => 'Batch status updated successfully'];
    }
    
    public function updateItemDetails($data) {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        $itemId = $data['item_id'] ?? 0;
        $status = $data['status'] ?? 'pending';
        $reply = $data['admin_reply'] ?? '';
        
        $docPath = null;
        if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/assets/userImage/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES['report_file']['name']);
            if (move_uploaded_file($_FILES['report_file']['tmp_name'], $uploadDir . $filename)) {
                $docPath = "/assets/userImage/" . $filename;
            }
        }
        
        $now = date("Y-m-d H:i:s");
        if ($docPath) {
            $stmt = $pdo->prepare("UPDATE bulk_nin_validation_items SET status = ?, admin_reply = ?, result_document = ?, date_updated = ? WHERE id = ?");
            $stmt->execute([$status, $reply, $docPath, $now, $itemId]);
        } else {
            $stmt = $pdo->prepare("UPDATE bulk_nin_validation_items SET status = ?, admin_reply = ?, date_updated = ? WHERE id = ?");
            $stmt->execute([$status, $reply, $now, $itemId]);
        }
        
        return ['status' => 'success', 'msg' => 'NIN item updated successfully'];
    }

    public function displayIpeClearanceRequests() {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        
        $status = $_GET['status'] ?? '';
        
        $sql = "SELECT r.*, s.sFname, s.sLname, s.sPhone FROM ipe_clearance_requests r LEFT JOIN subscribers s ON r.sId = s.sId WHERE 1=1";
        $params = [];
        
        if (!empty($status)) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY r.id DESC";
        $query = $pdo->prepare($sql);
        $query->execute($params);
        $requests = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($requests)) {
            return '<tr><td colspan="7" class="text-center">No IPE clearance requests found.</td></tr>';
        }
        
        $html = '';
        foreach ($requests as $req) {
            $statusBadge = '<span class="label label-' . ($req['status'] == 'completed' ? 'success' : ($req['status'] == 'processing' ? 'info' : ($req['status'] == 'failed' ? 'danger' : 'warning'))) . '">' . ucfirst($req['status']) . '</span>';
            
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($req['ref']) . '</td>';
            $html .= '<td>' . htmlspecialchars($req['sFname'] . ' ' . $req['sLname']) . '<br><small>' . htmlspecialchars($req['sPhone']) . '</small></td>';
            $html .= '<td>' . $req['total_tracking_ids'] . ' Tracking IDs</td>';
            $html .= '<td>₦' . number_format($req['total_amount'], 2) . '</td>';
            $html .= '<td>' . $statusBadge . '</td>';
            $html .= '<td>' . $req['date_created'] . '</td>';
            $html .= '<td>';
            $html .= '<a href="?url=ipe-clearance&view=' . $req['id'] . '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View Items</a> ';
            $html .= '<button class="btn btn-xs btn-success" onclick="updateIpeBatchStatusModal(' . $req['id'] . ', \'' . $req['status'] . '\')"><i class="fa fa-edit"></i> Status</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        return $html;
    }
    
    public function displayIpeBatchDetails($batchId) {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        
        $stmt = $pdo->prepare("SELECT r.*, s.sFname, s.sLname, s.sPhone, s.sEmail FROM ipe_clearance_requests r LEFT JOIN subscribers s ON r.sId = s.sId WHERE r.id = ?");
        $stmt->execute([$batchId]);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$batch) {
            return '<div class="alert alert-danger">Batch not found.</div>';
        }
        
        $itemStmt = $pdo->prepare("SELECT * FROM ipe_clearance_items WHERE batch_id = ?");
        $itemStmt->execute([$batchId]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $html = '<div class="box">
            <div class="box-header">
                <h3 class="box-title">IPE Batch Details: ' . htmlspecialchars($batch['ref']) . '</h3>
                <div class="box-tools">
                    <a href="?url=ipe-clearance" class="btn btn-default btn-sm">Back to List</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Customer:</strong> ' . htmlspecialchars($batch['sFname'] . ' ' . $batch['sLname']) . ' (' . htmlspecialchars($batch['sPhone']) . ')</p>
                        <p><strong>Total Tracking IDs:</strong> ' . $batch['total_tracking_ids'] . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Amount:</strong> ₦' . number_format($batch['total_amount'], 2) . '</p>
                        <p><strong>Status:</strong> <span class="label label-' . ($batch['status'] == 'completed' ? 'success' : 'warning') . '">' . ucfirst($batch['status']) . '</span></p>
                        <p><strong>Date:</strong> ' . $batch['date_created'] . '</p>
                    </div>
                </div>
                <hr>
                <h4>Submitted Tracking ID Items</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tracking ID</th>
                            <th>Status</th>
                            <th>Result Data / Notes</th>
                            <th>Result Document</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($items as $i => $item) {
            $html .= '<tr>';
            $html .= '<td>' . ($i + 1) . '</td>';
            $html .= '<td><strong>' . htmlspecialchars($item['tracking_id']) . '</strong></td>';
            $html .= '<td><span class="label label-' . ($item['status'] == 'completed' ? 'success' : ($item['status'] == 'failed' ? 'danger' : 'warning')) . '">' . ucfirst($item['status']) . '</span></td>';
            $html .= '<td>' . htmlspecialchars($item['result_data'] ?? 'Pending review') . '</td>';
            $html .= '<td>' . (!empty($item['result_document']) ? '<a href="' . $item['result_document'] . '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"></i> View Report</a>' : 'None') . '</td>';
            $html .= '<td><button class="btn btn-xs btn-primary" onclick="editIpeItem(' . $item['id'] . ', \'' . htmlspecialchars($item['tracking_id'], ENT_QUOTES) . '\', \'' . $item['status'] . '\', ' . htmlspecialchars(json_encode($item['result_data'] ?? ''), ENT_QUOTES) . ')">Update Item</button></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></div></div>';
        return $html;
    }
    
    public function updateIpeBatchStatus($data) {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        $batchId = $data['batch_id'] ?? 0;
        $status = $data['status'] ?? 'pending';
        $now = date("Y-m-d H:i:s");
        
        $stmt = $pdo->prepare("UPDATE ipe_clearance_requests SET status = ?, date_processed = ? WHERE id = ?");
        $stmt->execute([$status, $now, $batchId]);
        
        $itemStmt = $pdo->prepare("UPDATE ipe_clearance_items SET status = ?, date_updated = ? WHERE batch_id = ?");
        $itemStmt->execute([$status, $now, $batchId]);
        
        return ['status' => 'success', 'msg' => 'IPE Batch status updated successfully'];
    }
    
    public function updateIpeItemDetails($data) {
        $this->requireAdmin();
        $pdo = $this->ninModel->connect();
        $itemId = $data['item_id'] ?? 0;
        $status = $data['status'] ?? 'pending';
        $resultData = $data['result_data'] ?? '';
        
        $docPath = null;
        if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/assets/userImage/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES['report_file']['name']);
            if (move_uploaded_file($_FILES['report_file']['tmp_name'], $uploadDir . $filename)) {
                $docPath = "/assets/userImage/" . $filename;
            }
        }
        
        $now = date("Y-m-d H:i:s");
        if ($docPath) {
            $stmt = $pdo->prepare("UPDATE ipe_clearance_items SET status = ?, result_data = ?, result_document = ?, date_updated = ? WHERE id = ?");
            $stmt->execute([$status, $resultData, $docPath, $now, $itemId]);
        } else {
            $stmt = $pdo->prepare("UPDATE ipe_clearance_items SET status = ?, result_data = ?, date_updated = ? WHERE id = ?");
            $stmt->execute([$status, $resultData, $now, $itemId]);
        }
        
        return ['status' => 'success', 'msg' => 'IPE item updated successfully'];
    }
}
