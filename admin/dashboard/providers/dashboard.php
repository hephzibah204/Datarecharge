<?php
$action = $_GET['action'] ?? 'list';
$msg = '';

// Require controller if not already loaded
if (!class_exists('ProviderController')) {
    require_once __DIR__ . '/../../core/Controllers/ProviderController.php';
}

try {
    $controller = new ProviderController();
    
    switch ($action) {
        case 'add':
            echo $controller->displayProviderForm();
            break;
        case 'edit':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if ($id) {
                echo $controller->displayProviderForm($id);
            } else {
                echo '<div class="alert alert-danger">Provider ID not specified</div>';
            }
            break;
        case 'view':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if ($id) {
                echo $controller->displayProvidersView($id);
            } else {
                echo '<div class="alert alert-danger">Provider ID not specified</div>';
            }
            break;
        default:
            echo $controller->displayProvidersList();
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}
