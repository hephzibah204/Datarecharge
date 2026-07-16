<?php

class AirtimeProvider {
    public $name = 'airtime';
    public $type = 'airtime';
    public $endpoint = 'https://api.airtime.com/v1';
    public $apiKey = null;
    public $apiVersion = 'v1';
    public $actions = ['buy_airtime', 'check_balance', 'verify_transaction'];

    public function testConnection() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . '/balance');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['api_key' => $this->apiKey]));
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

    public function buyAirtime($phone, $amount, $network = null, $reference = null) {
        $data = [
            'phone' => $phone,
            'amount' => $amount
        ];
        if ($network) $data['network'] = $network;
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
}

?>
