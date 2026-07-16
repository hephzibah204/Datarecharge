<div class="row">
    <div class="col-12">
        <?php
        // Handle provider CRUD form submissions
        if (isset($_POST['action'])) {
            if (!class_exists('ProviderController')) {
                require_once __DIR__ . '/../core/Controllers/ProviderController.php';
            }
            $controller = new ProviderController();
            $result = $controller->handleProviderRequest($_POST['action'], $_POST['id'] ?? null);
            $alertClass = $result['status'] === 'success' ? 'success' : 'danger';
            echo '<div class="alert alert-' . $alertClass . ' alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    ' . $result['message'] . '
                  </div>';
        }

        $boxTitle = 'Provider Management';
        $action = $_GET['action'] ?? 'list';
        if ($action === 'add') $boxTitle = 'Add New Provider';
        elseif ($action === 'edit' && isset($_GET['id'])) $boxTitle = 'Edit Provider';
        elseif ($action === 'view' && isset($_GET['id'])) $boxTitle = 'Provider Details';
        ?>
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
                <h4 class="box-title"><?php echo $boxTitle; ?></h4>
                <div>
                    <a class="btn btn-info btn-sm btn-rounded" href="providers?action=add">+ Add New</a>
                    <a class="btn btn-secondary btn-sm btn-rounded" href="providers">Refresh</a>
                </div>
            </div>
            <div class="box-body">
                <?php include __DIR__ . '/providers/dashboard.php'; ?>
            </div>
        </div>
    </div>
</div>
