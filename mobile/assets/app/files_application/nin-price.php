<?php
header('Content-Type: application/json');

// Check if core Model is available (unified SQLite/MySQL architecture)
$modelPath = dirname(__DIR__, 4) . '/core/Models/Model.php';
if (file_exists($modelPath)) {
    require_once $modelPath;
    try {
        $model = new Model();
        $dbh = $model->connectDb();
        $stmt = $dbh->query("SELECT slip_name, buying_price, user_price FROM nin_price");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data && count($data) > 0) {
            echo json_encode(["success" => true, "message" => "Plans retrieved successfully", "data" => $data]);
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "No plans found", "data" => []]);
            exit;
        }
    } catch (Exception $e) {
        // Fall back to mysqli if PDO fails
    }
}

// Fallback to conn.php if legacy MySQL is being used
if (file_exists(__DIR__ . '/conn.php')) {
    include 'conn.php';
    if (isset($conn) && $conn) {
        $sql = "SELECT slip_name, buying_price, user_price FROM nin_price";
        $result = @mysqli_query($conn, $sql);
        $response = array();
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $response[] = $row;
            }
            echo json_encode(["success" => true, "message" => "Plans retrieved successfully", "data" => $response]);
            mysqli_close($conn);
            exit;
        }
    }
}

echo json_encode(["success" => false, "message" => "No plans found", "data" => []]);
?>