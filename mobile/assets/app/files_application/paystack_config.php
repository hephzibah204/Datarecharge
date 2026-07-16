<?php
// Your Paystack Secret Key
define('PAYSTACK_SECRET_KEY', 'sk_live_444b2bb61f333b75dc9fcee22c9877d2a25b5c12');

// Common headers for API requests
function getHeaders() {
    return [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Content-Type: application/json"
    ];
}
?>