<?php

// Provider API Package
// Export all provider classes

use ProviderAPI as GlobalProviderAPI;

require_once 'server.php';

// Export DataVerify Provider
require_once 'dataverify.php';

// Export Airtime Provider  
require_once 'airtime.php';

// Export Data Provider
require_once 'data.php';

// Export Cable TV Provider
require_once 'cabletv.php';

// Export Electricity Provider
require_once 'electricity.php';

// Export BVN Provider
require_once 'bvn.php';

// Export Exam Provider
require_once 'exam.php';

// Export Smile Data Provider
require_once 'smile-data.php';

// Export Alpha Topup Provider
require_once 'alpha-topup.php';

// Create a convenience function to get the default provider API
function getProviderAPI($configFilePath = null) {
    return createProviderAPI($configFilePath);
}

// Export the main ProviderAPI class
class ProviderAPI {
    // This is now re-exported from server.php
}

?>
