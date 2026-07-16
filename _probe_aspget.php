<?php
$key = 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7';

$endpoints = [
    'cac'            => 'https://api.aspget.com/cac/',
    'vin'            => 'https://api.aspget.com/vin/',
    'voter'          => 'https://api.aspget.com/voter/',
    'drivers-license'=> 'https://api.aspget.com/drivers-license/',
    'drivers_license'=> 'https://api.aspget.com/drivers_license/',
    'passport'       => 'https://api.aspget.com/passport/',
    'pnv'            => 'https://api.aspget.com/pnv/',
    'nin'            => 'https://api.aspget.com/nin/',
    'bvn'            => 'https://api.aspget.com/bvn/',
];

foreach ($endpoints as $name => $url) {
    echo "\n=== $name ($url) ===\n";
    
    // Try with empty payload first
    $payload = json_encode(["test" => true]);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $key"
        ],
    ]);
    
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP " . $info['http_code'] . "\n";
    if ($err) { echo "CURL Error: $err\n"; }
    if ($resp) {
        $decoded = json_decode($resp);
        if ($decoded) {
            echo "Response: " . json_encode($decoded, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "Raw: " . substr($resp, 0, 500) . "\n";
        }
    } else {
        echo "Empty response\n";
    }
}
