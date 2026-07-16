<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'conn.php';

// Check database connection
if (!$conn) {
    die(json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]));
}

// Initialize response array
$data = [];

// Fetch site settings (excluding app color)
$sql_site = "SELECT sitename AS app_name, siteurl, whatsappgroup, facebook AS facebook_link, phone, email, accountname, accountno AS account_number, bankname FROM sitesettings LIMIT 1";
$result_site = $conn->query($sql_site);

if ($result_site && $result_site->num_rows > 0) {
    $row_site = $result_site->fetch_assoc();
    
    $data[] = [
        "appname" => $row_site["app_name"] ?? "N/A",
        "siteurl" => $row_site["siteurl"] ?? "N/A",
        "whatsappgroup" => $row_site["whatsappgroup"] ?? "N/A",
        "facebooklink" => $row_site["facebook_link"] ?? "N/A",
        "phone" => $row_site["phone"] ?? "N/A",
        "email" => $row_site["email"] ?? "N/A",
        "accountnumber" => $row_site["account_number"] ?? "N/A",
        "bankname" => $row_site["bankname"] ?? "N/A",
        "accountname" => $row_site["accountname"] ?? "N/A"
    ];
} else {
    die(json_encode(["error" => "No data found in sitesettings table."]));
}

// Fetch app color from `app` table
$sql_app_color = "SELECT appcolor FROM app LIMIT 1";
$result_app_color = $conn->query($sql_app_color);

if ($result_app_color && $result_app_color->num_rows > 0) {
    $row_app_color = $result_app_color->fetch_assoc();
    $data[0]["appcolor"] = $row_app_color["appcolor"] ?? "N/A";
} else {
    $data[0]["appcolor"] = "N/A"; // Default if not found
}

// Fetch WhatsApp link from `app` table
$sql_whatsapp = "SELECT whatsapp_link FROM app LIMIT 1";
$result_whatsapp = $conn->query($sql_whatsapp);

if ($result_whatsapp && $result_whatsapp->num_rows > 0) {
    $row_whatsapp = $result_whatsapp->fetch_assoc();
    $data[0]["whatsapplink"] = $row_whatsapp["whatsapp_link"] ?? "N/A";
} else {
    $data[0]["whatsapplink"] = "N/A"; // Default if not found
}

// Fetch WhatsApp Link 2 from `app` table
$sql_whatsapp2 = "SELECT whatsapp_link2 FROM app LIMIT 1";
$result_whatsapp2 = $conn->query($sql_whatsapp2);

if ($result_whatsapp2 && $result_whatsapp2->num_rows > 0) {
    $row_whatsapp2 = $result_whatsapp2->fetch_assoc();
    $data[0]["whatsapplink2"] = $row_whatsapp2["whatsapp_link2"] ?? "N/A";
} else {
    $data[0]["whatsapplink2"] = "N/A"; // Default if not found
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);

// Close database connection
$conn->close();
?>