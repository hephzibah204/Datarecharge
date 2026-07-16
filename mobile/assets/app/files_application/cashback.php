<?php
// Include your database connection
require_once 'conn.php';

// Check if the table already has data
$query = "SELECT * FROM cashback_settings";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "The table already has data. No need to insert default values.";
} else {
    // SQL to insert default cashback values
    $insertQuery = "INSERT INTO cashback_settings (
        data_cashback, airtime_cashback, cable_cashback, exam_pin_cashback,
        electricity_cashback, wallet_cashback, recharge_cashback, data_card_cashback
    ) VALUES (5.00, 3.00, 2.00, 1.50, 4.00, 3.50, 2.50, 1.00)";
    
    if ($conn->query($insertQuery) === TRUE) {
        echo "Default cashback settings have been successfully inserted into the database.";
    } else {
        echo "Error inserting default values: " . $conn->error;
    }
}

$conn->close();
?>