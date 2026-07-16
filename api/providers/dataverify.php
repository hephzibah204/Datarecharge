<?php

class DataVerifyProvider {
    public $name = 'dataverify';
    public $type = 'data';
    public $endpoint = 'https://dataverify.com.ng';
    public $apiKey = null;
    public $actions = ['verify_nin', 'verify_bvn', 'verify_bank', 'ipe', 'ipe_status'];

    private function post($path, $data) {
        $payload = array_merge(['api_key' => $this->apiKey], $data);
        $encoded = json_encode($payload);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->endpoint . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $encoded,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($encoded),
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['status' => 'error', 'message' => 'Connection failed: ' . $curlError];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => 'error', 'message' => 'Invalid JSON response', 'raw' => $response];
        }

        $decoded['http_code'] = $httpCode;
        return $decoded;
    }

    public function testConnection() {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->endpoint . '/developers/nin_slips/nin_premium',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['api_key' => $this->apiKey, 'nin' => '00000000000']),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['connected' => $httpCode !== 0, 'http_code' => $httpCode];
    }

    public function verifyNIN($nin) {
        return $this->post('/developers/nin_slips/nin_premium', ['nin' => $nin]);
    }

    public function verifyNINByPhone($phone) {
        return $this->post('/developers/nin_slips/nin_by_phone.php', ['phone' => $phone]);
    }

    public function verifyNINByDemo($firstname, $lastname, $dob, $gender) {
        $g = strtolower($gender);
        if ($g === 'male') $g = 'm';
        if ($g === 'female') $g = 'f';
        return $this->post('/developers/nin_slips/nin_premium_demo.php', [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'dob'       => $dob,
            'gender'    => $g,
        ]);
    }

    public function verifyBVN($bvn) {
        return $this->post('/developers/bvn_slip/bvn_premium.php', ['bvn' => $bvn]);
    }

    public function verifyBankAccount($bvn, $bankCode, $bankAccount) {
        return $this->post('/developers/nin_slips/bank_account_verify.php', [
            'bvn'         => $bvn,
            'bankCode'    => $bankCode,
            'bankAccount' => $bankAccount,
        ]);
    }

    public function ipeClearance($trackingID) {
        return $this->post('/api/developers/ipe2.php', ['trackingID' => $trackingID]);
    }

    public function ipeStatus($trackingID) {
        return $this->post('/api/developers/ipe_status2.php', ['trackingID' => $trackingID]);
    }
}
