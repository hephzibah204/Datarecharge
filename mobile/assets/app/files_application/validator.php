<?php  
// Enable error reporting for debugging (remove in production)  
ini_set('display_errors', 1);  
ini_set('display_startup_errors', 1);  
error_reporting(E_ALL);

// Set text/plain content type
header("Content-Type: text/plain");

// Get the phone number from the POST request  
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone'])) {  
    $phoneNumber = $_POST['phone'];  

    // Remove any non-digit characters  
    $phoneNumber = preg_replace('/\D/', '', $phoneNumber);  

    // Ensure the number is 11 digits  
    if (strlen($phoneNumber) != 11) {  
        exit; // No output if invalid
    }  

    // Define network prefixes  
    $gloPrefixes = ['0805', '0705', '0905', '0807', '0907', '0707', '0817', '0917', '0717', '0715', '0815', '0915', '0811', '0711', '0911'];  
    $mtnPrefixes = ['0702', '0703', '0713', '0704', '0706', '0716', '0802', '0803', '0806', '0810', '0813', '0814', '0816', '0903', '0913', '0906', '0916'];  
    $airtelPrefixes = ['0904', '0802', '0902', '0702', '0808', '0908', '0708', '0918', '0818', '0718', '0812', '0912', '0712', '0801', '0701', '0901'];  
    $etisalatPrefixes = ['0809', '0909', '0709', '0819', '0919', '0719', '0817', '0917', '0717', '0718', '0918', '0818', '0808', '0708', '0908'];  

    // Extract the first 4 digits  
    $prefix = substr($phoneNumber, 0, 4);  

    // Check network and print the name directly  
    if (in_array($prefix, $mtnPrefixes)) {  
        echo "MTN";  
    } elseif (in_array($prefix, $airtelPrefixes)) {  
        echo "Airtel";  
    } elseif (in_array($prefix, $gloPrefixes)) {  
        echo "Glo";  
    } elseif (in_array($prefix, $etisalatPrefixes)) {  
        echo "9mobile";  
    }  
}  
?>