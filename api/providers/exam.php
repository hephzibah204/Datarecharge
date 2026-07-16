<?php

class ExamProvider {
    public $name = 'exam';
    public $type = 'exam';
    public $endpoint = 'https://api.exam.com/v1';
    public $apiKey = null;
    public $apiVersion = 'v1';
    public $actions = ['buy', 'verify', 'check_result'];

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

    public function buy($pin_type, $quantity, $reference = null) {
        $data = [
            'pin_type' => $pin_type,
            'quantity' => $quantity
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

    public function verify($pin_code) {
        $data = ['pin' => $pin_code];
        
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
