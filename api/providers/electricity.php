<?php

class ElectricityProvider {
    public $name = 'electricity';
    public $type = 'electricity';
    public $endpoint = 'https://api.electricity.com/v1';
    public $apiKey = null;
    public $apiVersion = 'v1';
    public $actions = ['pay_bill', 'verify', 'check_balance'];

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

    public function payBill($meter_number, $amount, $disco = null) {
        $data = [
            'meter_number' => $meter_number,
            'amount' => $amount
        ];
        if ($disco) $data['disco'] = $disco;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . '/pay');
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
}

?>
