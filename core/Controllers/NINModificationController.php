<?php

class NINModificationController extends AdminController {
    
    public $helper;
    
    public function __construct() {
        parent::__construct();
        $helperPath = __DIR__ . '/../helpers/vendor/modification_helper.php';
        if (file_exists($helperPath)) {
            require_once $helperPath;
            $this->helper = new ModificationHelper();
        }
    }
    
    public function displayModificationRequests() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 1000;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? 'all';
        
        $requests = $this->getModificationRequests($limit, $offset, $status);
        $count = $this->getModificationRequestCount($status);
        $total_pages = ceil($count / $limit);
        
        $html = '';
        if ($total_pages > 1) {
            $html .= '<div class="pagination-container">';
            $html .= '<ul class="pagination">';
            if ($page > 1) {
                $html .= '<li><a href="?page=' . ($page - 1) . ($status !== 'all' ? '&status=' . $status : '') . '">Previous</a></li>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i === $page ? ' class="active"' : '';
                $html .= '<li' . $active . '><a href="?page=' . $i . ($status !== 'all' ? '&status=' . $status : '') . '">' . $i . '</a></li>';
            }
            if ($page < $total_pages) {
                $html .= '<li><a href="?page=' . ($page + 1) . ($status !== 'all' ? '&status=' . $status : '') . '">Next</a></li>';
            }
            $html .= '</ul></div>';
        }
        
        if (empty($requests)) {
            $html .= '<div class="alert alert-info">No modification requests found.</div>';
        } else {
            foreach ($requests as $request) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($request->ref ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars(($request->sFname ?? '') . ' ' . ($request->sLname ?? '')) . '</td>';
                $html .= '<td>' . htmlspecialchars($request->sPhone ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($request->type ?? '') . '</td>';
                $html .= '<td>N' . number_format((float)($request->fee ?? 0), 2) . '</td>';
                $html .= '<td><span class="label label-' . $this->getStatusClass($request->status) . '">' . htmlspecialchars($request->status) . '</span></td>';
                $html .= '<td>' . date('M d, Y h:i A', strtotime($request->date_created ?? '')) . '</td>';
                $html .= '<td>
                    <a href="nin-modifications?view=' . urlencode($request->ref ?? '') . '" class="btn btn-info btn-xs">View</a>
                    <button class="btn btn-warning btn-xs review-request" data-ref="' . htmlspecialchars($request->ref ?? '') . '">Review</button>
                </td>';
                $html .= '</tr>';
            }
        }
        
        return $html;
    }
    
    public function displayModificationRequestDetails() {
        $ref = $_GET['view'] ?? '';
        $request = $this->getModificationByRef($ref);
        
        if (!$request) {
            return '<div class="alert alert-danger">Modification request not found.</div>';
        }
        
        $html = '<div class="row">';
        $html .= '<div class="col-md-6"><h4>Request Information</h4><hr><table class="table table-bordered">';
        $html .= '<tr><th>Ref</th><td>' . htmlspecialchars($request->ref ?? '') . '</td></tr>';
        $html .= '<tr><th>User</th><td>' . htmlspecialchars($request->sId ?? '') . '</td></tr>';
        $html .= '<tr><th>Type</th><td>' . htmlspecialchars($request->type ?? '') . '</td></tr>';
        $html .= '<tr><th>New Value</th><td>' . htmlspecialchars($request->new_value ?? '') . '</td></tr>';
        $html .= '<tr><th>Reason</th><td>' . htmlspecialchars($request->reason ?? '') . '</td></tr>';
        $html .= '<tr><th>Fee</th><td>N' . number_format((float)($request->fee ?? 0), 2) . '</td></tr>';
        $html .= '<tr><th>Status</th><td><span class="label label-' . $this->getStatusClass($request->status) . '">' . htmlspecialchars($request->status ?? '') . '</span></td></tr>';
        $html .= '<tr><th>Created</th><td>' . htmlspecialchars($request->date_created ?? '') . '</td></tr>';
        $html .= '<tr><th>Reviewed By</th><td>' . htmlspecialchars($request->reviewed_by ?? 'Not reviewed') . '</td></tr>';
        $html .= '<tr><th>Reviewed Date</th><td>' . htmlspecialchars($request->date_reviewed ?? 'Not reviewed') . '</td></tr>';
        if (!empty($request->result_document)) {
            $html .= '<tr><th>Completed Report</th><td><a href="' . htmlspecialchars($request->result_document) . '" download class="btn btn-success btn-xs">Download Report</a></td></tr>';
        }
        $html .= '</table></div>';

        $documents = json_decode($request->documents ?? '[]');
        if (!empty($documents)) {
            $html .= '<div class="col-md-6"><h4>Uploaded Documents</h4><hr><ul class="list-group">';
            foreach ($documents as $doc) {
                $docType = htmlspecialchars($doc->type ?? 'Document');
                $docPath = htmlspecialchars($doc->path ?? '');
                $html .= '<li class="list-group-item" style="overflow: hidden;">';
                $html .= '<span><strong>' . ucwords(str_replace('_', ' ', $docType)) . ':</strong></span>';
                $html .= ' <a href="' . $docPath . '" target="_blank" class="btn btn-info btn-xs pull-right">View / Download</a>';
                $html .= '</li>';
            }
            $html .= '</ul></div>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    public function getModificationRequests($limit, $offset, $status) {
        $model = new Modification();
        return $model->getModificationRequests($limit, $offset, $status);
    }
    
    public function getModificationRequestCount($status) {
        $model = new Modification();
        return $model->getModificationRequestCount($status);
    }
    
    public function getModificationByRef($ref) {
        $model = new Modification();
        return $model->getModificationByRef($ref);
    }
    
    public function updateModificationStatus($request) {
        $ref = $request['ref'] ?? '';
        $status = $request['status'] ?? '';
        $adminNotes = $request['admin_notes'] ?? '';
        
        $validStatuses = ['pending', 'in_review', 'approved', 'declined', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return ['status' => 'fail', 'msg' => 'Invalid status'];
        }

        $reportPath = null;
        if (isset($_FILES['report_file']) && !empty($_FILES['report_file']['name']) && $_FILES['report_file']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES['report_file']['name']);
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['report_file']['tmp_name'], $destination)) {
                $reportPath = "/assets/reports/" . $filename;
            }
        }
        
        $model = new Modification();
        $model->updateModificationStatus($ref, $status, $this->getCurrentUserId(), $adminNotes, $reportPath);
        
        return ['status' => 'success', 'msg' => 'Request status updated'];
    }
    
    public function getStatusClass($status) {
        $classes = [
            'pending' => 'warning',
            'in_review' => 'info',
            'approved' => 'success',
            'declined' => 'danger',
            'completed' => 'primary',
            'cancelled' => 'default'
        ];
        return $classes[$status] ?? 'default';
    }
    
    protected function getCurrentUserId() {
        return self::$sysId ?? $_SESSION['sysId'] ?? 0;
    }
}
