<?php

class JobRequestController extends AdminController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get summary counts for all job requests across tables
     */
    public function getJobCounts() {
        $dbh = $this->model->dbh;

        // Pending counts
        $ninPending = 0;
        $cacPending = 0;
        $ipePending = 0;
        $bulkPending = 0;

        try {
            $stmt = $dbh->query("SELECT COUNT(*) FROM nin_modifications WHERE status = 'pending'");
            $ninPending = (int)($stmt->fetchColumn() ?: 0);
        } catch (Exception $e) {}

        try {
            $stmt = $dbh->query("SELECT COUNT(*) FROM CAC WHERE status IS NULL OR status = '' OR LOWER(status) = 'pending'");
            $cacPending = (int)($stmt->fetchColumn() ?: 0);
        } catch (Exception $e) {}

        try {
            $stmt = $dbh->query("SELECT COUNT(*) FROM ipe_clearance_requests WHERE status = 'pending'");
            $ipePending = (int)($stmt->fetchColumn() ?: 0);
        } catch (Exception $e) {}

        try {
            $stmt = $dbh->query("SELECT COUNT(*) FROM bulk_nin_validation_requests WHERE status = 'pending'");
            $bulkPending = (int)($stmt->fetchColumn() ?: 0);
        } catch (Exception $e) {}

        // Total in review / processing
        $inReview = 0;
        try {
            $stmt = $dbh->query("SELECT COUNT(*) FROM nin_modifications WHERE status = 'in_review'");
            $inReview += (int)($stmt->fetchColumn() ?: 0);
        } catch (Exception $e) {}

        try {
            $stmt = $dbh->query("SELECT COUNT(*) FROM CAC WHERE LOWER(status) = 'in_review' OR LOWER(status) = 'processing'");
            $inReview += (int)($stmt->fetchColumn() ?: 0);
        } catch (Exception $e) {}

        return [
            'total_pending' => $ninPending + $cacPending + $ipePending + $bulkPending,
            'nin_pending' => $ninPending,
            'cac_pending' => $cacPending,
            'ipe_pending' => $ipePending,
            'bulk_pending' => $bulkPending,
            'in_review' => $inReview
        ];
    }

    /**
     * Fetch unified list of job requests
     */
    public function getJobRequests($category = 'all', $status = 'all', $limit = 100, $offset = 0) {
        $dbh = $this->model->dbh;
        $jobs = [];

        // 1. Fetch from nin_modifications (NIN Modifications, Affidavits, Birth Attestations)
        if ($category === 'all' || in_array($category, ['nin', 'affidavit', 'birth_cert', 'modifications'])) {
            $sql = "SELECT nm.id, nm.ref, nm.type, nm.new_value, nm.reason, nm.fee, nm.status, 
                           nm.date_created, nm.date_reviewed, nm.reviewed_by, nm.admin_notes, nm.documents, nm.result_document,
                           s.sFname, s.sLname, s.sPhone, s.sEmail, s.sState
                    FROM nin_modifications nm 
                    LEFT JOIN subscribers s ON nm.sId = s.sId";
            
            $where = [];
            $params = [];

            if ($status !== 'all' && !empty($status)) {
                $where[] = "nm.status = :status";
                $params[':status'] = $status;
            }

            if ($category === 'affidavit') {
                $where[] = "LOWER(nm.type) LIKE '%affidavit%'";
            } elseif ($category === 'birth_cert') {
                $where[] = "LOWER(nm.type) LIKE '%birth%'";
            } elseif ($category === 'nin') {
                $where[] = "LOWER(nm.type) NOT LIKE '%affidavit%' AND LOWER(nm.type) NOT LIKE '%birth%'";
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY nm.id DESC LIMIT 150";

            try {
                $stmt = $dbh->prepare($sql);
                $stmt->execute($params);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $jobType = $row['type'] ?? 'NIN Modification';
                    $cat = 'NIN Modification';
                    $badge = 'info';

                    if (stripos($jobType, 'affidavit') !== false) {
                        $cat = 'Affidavit';
                        $badge = 'warning';
                    } elseif (stripos($jobType, 'birth') !== false) {
                        $cat = 'Birth Attestation';
                        $badge = 'purple';
                    }

                    $jobs[] = [
                        'source' => 'nin_modifications',
                        'id' => $row['id'],
                        'ref' => $row['ref'] ?: ('MOD-' . $row['id']),
                        'category' => $cat,
                        'category_badge' => $badge,
                        'service_title' => ucwords(str_replace('_', ' ', $jobType)),
                        'customer_name' => trim(($row['sFname'] ?? '') . ' ' . ($row['sLname'] ?? '')) ?: 'Customer #' . ($row['sId'] ?? ''),
                        'customer_phone' => $row['sPhone'] ?? 'N/A',
                        'customer_email' => $row['sEmail'] ?? '',
                        'details' => $row['new_value'] ? 'New Value: ' . $row['new_value'] : ($row['reason'] ?: 'No description'),
                        'fee' => (float)($row['fee'] ?? 0),
                        'status' => strtolower($row['status'] ?: 'pending'),
                        'date_created' => $row['date_created'] ?: '',
                        'reviewed_by' => $row['reviewed_by'] ?: null,
                        'admin_notes' => $row['admin_notes'] ?: '',
                        'documents' => $row['documents'] ?: '[]',
                        'result_document' => $row['result_document'] ?: '',
                        'view_url' => 'nin-modifications?view=' . urlencode($row['ref'])
                    ];
                }
            } catch (Exception $e) {}
        }

        // 2. Fetch from CAC table (CAC Registrations)
        if ($category === 'all' || $category === 'cac') {
            $sql = "SELECT c.id, c.certType, c.email, c.comp_name, c.alt_comp_name, c.share_cap, 
                           c.comp_addr, c.res_addr, c.bus_nature, c.dir_id_card, c.passport_photo, 
                           c.phone_num, c.status, c.sId, c.submit_date, c.note,
                           s.sFname, s.sLname, s.sPhone, s.sEmail
                    FROM CAC c
                    LEFT JOIN subscribers s ON c.sId = s.sId";

            $where = [];
            $params = [];

            if ($status !== 'all' && !empty($status)) {
                if ($status === 'pending') {
                    $where[] = "(c.status IS NULL OR c.status = '' OR LOWER(c.status) = 'pending')";
                } else {
                    $where[] = "LOWER(c.status) = :status";
                    $params[':status'] = strtolower($status);
                }
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY c.id DESC LIMIT 100";

            try {
                $stmt = $dbh->prepare($sql);
                $stmt->execute($params);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rawStatus = strtolower(trim($row['status'] ?? ''));
                    $normStatus = ($rawStatus === '' || $rawStatus === 'null') ? 'pending' : $rawStatus;
                    if ($normStatus === 'approved') $normStatus = 'approved';
                    if ($normStatus === 'rejected') $normStatus = 'declined';

                    $certTitle = ($row['certType'] === 'biz') ? 'Business Name Reg' : 'Limited Liability Reg';
                    $compName = $row['comp_name'] . ($row['alt_comp_name'] ? ' / ' . $row['alt_comp_name'] : '');

                    $docs = [];
                    if (!empty($row['dir_id_card'])) {
                        $docs[] = ['type' => 'Director ID', 'path' => $row['dir_id_card']];
                    }
                    if (!empty($row['passport_photo'])) {
                        $docs[] = ['type' => 'Passport Photo', 'path' => $row['passport_photo']];
                    }

                    $jobs[] = [
                        'source' => 'cac',
                        'id' => $row['id'],
                        'ref' => 'CAC-' . $row['id'],
                        'category' => 'CAC Registration',
                        'category_badge' => 'primary',
                        'service_title' => $certTitle . ' (' . $compName . ')',
                        'customer_name' => trim(($row['sFname'] ?? '') . ' ' . ($row['sLname'] ?? '')) ?: $row['email'],
                        'customer_phone' => $row['phone_num'] ?: ($row['sPhone'] ?? 'N/A'),
                        'customer_email' => $row['email'] ?: ($row['sEmail'] ?? ''),
                        'details' => 'Nature of Biz: ' . ($row['bus_nature'] ?: 'N/A') . ' | Address: ' . ($row['comp_addr'] ?: 'N/A'),
                        'fee' => 0, // fee handled in transaction logs
                        'status' => $normStatus,
                        'date_created' => $row['submit_date'] ?: '',
                        'reviewed_by' => null,
                        'admin_notes' => $row['note'] ?: '',
                        'documents' => json_encode($docs),
                        'result_document' => '',
                        'view_url' => 'cac'
                    ];
                }
            } catch (Exception $e) {}
        }

        // 3. Fetch from IPE Clearance Requests
        if ($category === 'all' || $category === 'ipe') {
            $sql = "SELECT r.id, r.ref, r.total_tracking_ids, r.base_fee, r.total_amount, r.status, 
                           r.date_created, r.date_completed,
                           s.sFname, s.sLname, s.sPhone, s.sEmail
                    FROM ipe_clearance_requests r
                    LEFT JOIN subscribers s ON r.user_id = s.sId";

            $where = [];
            $params = [];

            if ($status !== 'all' && !empty($status)) {
                $where[] = "r.status = :status";
                $params[':status'] = $status;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY r.id DESC LIMIT 50";

            try {
                $stmt = $dbh->prepare($sql);
                $stmt->execute($params);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $jobs[] = [
                        'source' => 'ipe',
                        'id' => $row['id'],
                        'ref' => $row['ref'] ?: ('IPE-' . $row['id']),
                        'category' => 'IPE Clearance',
                        'category_badge' => 'success',
                        'service_title' => 'IPE Clearance Batch (' . ($row['total_tracking_ids'] ?? 1) . ' IDs)',
                        'customer_name' => trim(($row['sFname'] ?? '') . ' ' . ($row['sLname'] ?? '')) ?: 'User #' . ($row['id'] ?? ''),
                        'customer_phone' => $row['sPhone'] ?? 'N/A',
                        'customer_email' => $row['sEmail'] ?? '',
                        'details' => 'Tracking IDs Count: ' . ($row['total_tracking_ids'] ?? 1),
                        'fee' => (float)($row['total_amount'] ?? 0),
                        'status' => strtolower($row['status'] ?: 'pending'),
                        'date_created' => $row['date_created'] ?: '',
                        'reviewed_by' => null,
                        'admin_notes' => '',
                        'documents' => '[]',
                        'result_document' => '',
                        'view_url' => 'ipe-clearance?view=' . $row['id']
                    ];
                }
            } catch (Exception $e) {}
        }

        // Sort all jobs by date_created / id DESC
        usort($jobs, function($a, $b) {
            $tA = strtotime($a['date_created'] ?: '2000-01-01');
            $tB = strtotime($b['date_created'] ?: '2000-01-01');
            if ($tA == $tB) {
                return $b['id'] <=> $a['id'];
            }
            return $tB <=> $tA;
        });

        return $jobs;
    }

    /**
     * Pick up a request (sets status to 'in_review' / 'processing' and claims for the admin)
     */
    public function pickUpJob($source, $id, $ref = '') {
        $adminId = self::$sysId ?? $_SESSION['sysId'] ?? 1;
        $adminName = self::$adminName ?? $_SESSION['sysName'] ?? 'Admin';
        $dbh = $this->model->dbh;

        if ($source === 'nin_modifications') {
            $stmt = $dbh->prepare("UPDATE nin_modifications SET status = 'in_review', reviewed_by = :admin, date_reviewed = datetime('now','localtime') WHERE id = :id OR ref = :ref");
            $stmt->execute([':admin' => $adminId, ':id' => $id, ':ref' => $ref]);
            return ['status' => 'success', 'msg' => "Job ({$ref}) picked up successfully by {$adminName}! Status changed to In Review."];
        }

        if ($source === 'cac') {
            $stmt = $dbh->prepare("UPDATE CAC SET status = 'in_review', note = :note WHERE id = :id");
            $stmt->execute([':id' => $id, ':note' => "Picked up by {$adminName} on " . date('Y-m-d H:i')]);
            return ['status' => 'success', 'msg' => "CAC Registration #{$id} picked up successfully! Status changed to In Review."];
        }

        if ($source === 'ipe') {
            $stmt = $dbh->prepare("UPDATE ipe_clearance_requests SET status = 'processing' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return ['status' => 'success', 'msg' => "IPE Clearance Batch #{$id} picked up successfully! Status changed to Processing."];
        }

        return ['status' => 'fail', 'msg' => 'Unsupported job source.'];
    }

    /**
     * Complete or update a job review
     */
    public function completeJobReview($post, $files = []) {
        $source = $post['source'] ?? '';
        $id = $post['id'] ?? 0;
        $ref = $post['ref'] ?? '';
        $status = strtolower($post['status'] ?? 'completed');
        $notes = trim($post['admin_notes'] ?? '');
        $adminId = self::$sysId ?? $_SESSION['sysId'] ?? 1;
        $adminName = self::$adminName ?? $_SESSION['sysName'] ?? 'Admin';
        $dbh = $this->model->dbh;

        // Handle uploaded result document/report if provided
        $resultDoc = null;
        if (isset($files['report_file']) && !empty($files['report_file']['name']) && $files['report_file']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $files['report_file']['name']);
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($files['report_file']['tmp_name'], $destination)) {
                $resultDoc = "/assets/reports/" . $filename;
            }
        }

        if ($source === 'nin_modifications') {
            $sql = "UPDATE nin_modifications SET status = :status, admin_notes = :notes, reviewed_by = :admin, date_reviewed = datetime('now','localtime')";
            $params = [':status' => $status, ':notes' => $notes, ':admin' => $adminId, ':id' => $id, ':ref' => $ref];
            if ($resultDoc) {
                $sql .= ", result_document = :doc";
                $params[':doc'] = $resultDoc;
            }
            $sql .= " WHERE id = :id OR ref = :ref";

            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            return ['status' => 'success', 'msg' => "Request {$ref} review saved successfully! Status: " . ucfirst($status)];
        }

        if ($source === 'cac') {
            $cacStatus = ($status === 'approved' || $status === 'completed') ? 'Approved' : (($status === 'declined' || $status === 'rejected') ? 'Rejected' : ucfirst($status));
            $email = $post['email'] ?? '';
            $check = $this->model->updateCACStatus($id, $email, $cacStatus, $notes);
            return ['status' => ($check == 0 ? 'success' : 'fail'), 'msg' => "CAC Request #{$id} status updated to {$cacStatus}."];
        }

        if ($source === 'ipe') {
            $stmt = $dbh->prepare("UPDATE ipe_clearance_requests SET status = :status, date_completed = datetime('now','localtime') WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            return ['status' => 'success', 'msg' => "IPE Batch #{$id} status updated to " . ucfirst($status) . "."];
        }

        return ['status' => 'fail', 'msg' => 'Unsupported job source.'];
    }

    public function getStatusBadgeClass($status) {
        $status = strtolower($status);
        switch ($status) {
            case 'pending': return 'warning';
            case 'in_review':
            case 'processing': return 'info';
            case 'approved':
            case 'completed': return 'success';
            case 'declined':
            case 'failed':
            case 'rejected': return 'danger';
            default: return 'default';
        }
    }
}
