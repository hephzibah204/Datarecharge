<?php

class DataProvider {
    public $name = 'data';
    public $type = 'data';
    public $endpoint = 'https://api.mobile-data.com/v1';
    public $apiKey = null;
    public $apiVersion = 'v1';
    public $actions = ['buy_data', 'verify_subscriber', 'check_balance'];

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

    public function buyData($phone, $amount, $data_type = null, $reference = null) {
        $data = [
            'phone' => $phone,
            'amount' => $amount,
            'type' => $data_type
        ];
        if ($reference) $data['reference'] = $reference;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . '/buy');
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
        $curlError = curl_error($ch);
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        $decodedResponse['http_code'] = $httpCode;
        $decodedResponse['curl_error'] = $curlError;
        
        return $decodedResponse;
    }

    public function verifySubscriber($phone, $network = null) {
        $data = ['phone' => $phone];
        if ($network) $data['network'] = $network;
        
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
