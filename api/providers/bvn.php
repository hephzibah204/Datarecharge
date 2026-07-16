<?php

class BVNProvider {
    public $name = 'bvn';
    public $type = 'bvn';
    public $endpoint = 'https://api.bvn.com/v1';
    public $apiKey = null;
    public $apiVersion = 'v1';
    public $actions = ['verify', 'check_balance'];

    public function testConnection() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        return [
            'connected' => ($httpCode >= 200 && $httpCode < 300),
            'http_code' => $httpCode,
            'response' => $decodedResponse
        ];
    }

    public function verify($bvn, $phone = null) {
        $data = ['bvn' => $bvn];
        if ($phone) $data['phone'] = $phone;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . '/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $decodedResponse = json_decode($response, true);
        
        return $decodedResponse;
    }
}

?>
