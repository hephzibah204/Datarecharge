<?php

class DataVerifyProvider {
    public $name = 'dataverify';
    public $type = 'data';
    public $endpoint = 'https://dataverify.org';
    public $apiKey = null;
    public $actions = [
        'balance',
        'verify_nin',
        'verify_nin_phone',
        'verify_nin_demo',
        'verify_bvn',
        'verify_bank',
        'ipe',
        'ipe_status',
        'validate_nin',
        'validation_status',
        'personalization',
        'personalization_status',
        'bvn_retrieval',
        'bvn_retrieval_status'
    ];

    public function __construct($apiKey = null, $endpoint = null) {
        if ($apiKey !== null) {
            $this->apiKey = $apiKey;
        }
        if ($endpoint !== null) {
            $this->endpoint = rtrim($endpoint, '/');
        }
    }

    private function getHeaders($extra = []) {
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        if (!empty($this->apiKey)) {
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
            $headers[] = 'X-API-Key: ' . $this->apiKey;
        }
        return array_merge($headers, $extra);
    }

    private function post($path, $data = []) {
        $payload = array_merge(['api_key' => $this->apiKey], $data);
        $encoded = json_encode($payload);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => rtrim($this->endpoint, '/') . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $encoded,
            CURLOPT_HTTPHEADER     => $this->getHeaders(['Content-Length: ' . strlen($encoded)]),
            CURLOPT_TIMEOUT        => 35,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['status' => false, 'message' => 'Connection failed: ' . $curlError, 'http_code' => $httpCode];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => false, 'message' => 'Invalid JSON response', 'raw' => $response, 'http_code' => $httpCode];
        }

        $decoded['http_code'] = $httpCode;
        return $decoded;
    }

    private function get($path, $params = []) {
        $url = rtrim($this->endpoint, '/') . $path;
        if (!empty($params)) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $this->getHeaders(),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['status' => false, 'message' => 'Connection failed: ' . $curlError, 'http_code' => $httpCode];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => false, 'message' => 'Invalid JSON response', 'raw' => $response, 'http_code' => $httpCode];
        }

        $decoded['http_code'] = $httpCode;
        return $decoded;
    }

    /**
     * Check wallet balance & optionally fetch live service prices
     */
    public function getBalance($includePrices = false) {
        $params = $includePrices ? ['include_prices' => 1] : [];
        return $this->get('/api/developers/balance.php', $params);
    }

    /**
     * Test connection to DataVerify
     */
    public function testConnection() {
        $res = $this->getBalance();
        $isConnected = !empty($res['status']) && ($res['status'] === true || $res['status'] === 'success' || isset($res['balance']));
        return [
            'connected' => $isConnected,
            'http_code' => $res['http_code'] ?? 0,
            'data'      => $res
        ];
    }

    /**
     * NIN Verification Slip by 11-digit NIN
     */
    public function verifyNIN($nin) {
        return $this->post('/developers/nin_slips/nin_premium.php', ['nin' => trim($nin)]);
    }

    /**
     * NIN Verification Slip by Nigerian Phone Number
     * Note: DataVerify documentation specifies {"api_key": "...", "nin": "<phone_number>"}
     */
    public function verifyNINByPhone($phone) {
        return $this->post('/developers/nin_slips/nin_by_phone.php', ['nin' => trim($phone)]);
    }

    /**
     * NIN Verification Slip by Demographic Details
     */
    public function verifyNINByDemo($firstname, $lastname, $dob, $gender) {
        $g = strtolower(trim($gender));
        if ($g === 'male' || $g === 'm') $g = 'm';
        if ($g === 'female' || $g === 'f') $g = 'f';

        return $this->post('/developers/nin_slips/nin_premium_demo.php', [
            'firstname' => trim($firstname),
            'lastname'  => trim($lastname),
            'dob'       => trim($dob),
            'gender'    => $g,
        ]);
    }

    /**
     * BVN Verification Slip
     */
    public function verifyBVN($bvn) {
        return $this->post('/developers/bvn_slip/bvn_premium.php', ['bvn' => trim($bvn)]);
    }

    /**
     * Bank Account & Name Match Verification
     */
    public function verifyBankAccount($bvn, $bankCode, $bankAccount) {
        return $this->post('/developers/nin_slips/bank_account_verify.php', [
            'bvn'         => trim($bvn),
            'bankCode'    => trim($bankCode),
            'bankAccount' => trim($bankAccount),
        ]);
    }

    /**
     * NIN Validation Submit
     * Allowed types: no_record_found, sim_validation, bank_validation, modification_validation, photographic_error
     */
    public function validateNIN($nin, $validationType = 'no_record_found') {
        $allowedTypes = [
            'no_record_found',
            'sim_validation',
            'bank_validation',
            'modification_validation',
            'photographic_error',
        ];

        $map = [
            'no record found'           => 'no_record_found',
            'sim validation'            => 'sim_validation',
            'bank validation'           => 'bank_validation',
            'modification validation'   => 'modification_validation',
            'photographic error'        => 'photographic_error',
            'standard'                  => 'no_record_found',
            'premium'                   => 'sim_validation',
            'modification'              => 'modification_validation',
        ];

        $key = strtolower(trim($validationType));
        if (isset($map[$key])) {
            $validationType = $map[$key];
        }

        if (!in_array($validationType, $allowedTypes)) {
            $validationType = 'no_record_found';
        }

        return $this->post('/api/developers/validation.php', [
            'nin'             => trim($nin),
            'validation_type' => $validationType
        ]);
    }

    /**
     * Check NIN Validation Status by transaction_id or nin
     */
    public function checkValidationStatus($transactionIdOrNin) {
        $val = trim($transactionIdOrNin);
        $payload = (ctype_digit($val) && strlen($val) === 11) ? ['nin' => $val] : ['transaction_id' => $val];
        return $this->post('/api/developers/validation_status.php', $payload);
    }

    /**
     * IPE Clearance - Submit Request
     */
    public function ipeClearance($trackingID) {
        $res = $this->post('/api/developers/ipe.php', ['trackingID' => trim($trackingID)]);
        if (isset($res['http_code']) && ($res['http_code'] === 404 || (isset($res['status']) && $res['status'] === false && strpos($res['message'] ?? '', 'Not Found') !== false))) {
            return $this->post('/api/developers/ipe2.php', ['trackingID' => trim($trackingID)]);
        }
        return $res;
    }

    /**
     * IPE Clearance - Check Status
     */
    public function ipeStatus($trackingID) {
        $res = $this->post('/api/developers/ipe_status.php', ['trackingID' => trim($trackingID)]);
        if (isset($res['http_code']) && ($res['http_code'] === 404 || (isset($res['status']) && $res['status'] === false && strpos($res['message'] ?? '', 'Not Found') !== false))) {
            return $this->post('/api/developers/ipe_status2.php', ['trackingID' => trim($trackingID)]);
        }
        return $res;
    }

    /**
     * Personalization - Submit Request
     */
    public function submitPersonalization($trackingId) {
        return $this->post('/api/developers/personalization.php', ['tracking_id' => trim($trackingId)]);
    }

    /**
     * Personalization - Check Status
     */
    public function checkPersonalizationStatus($transactionIdOrTrackingId) {
        $val = trim($transactionIdOrTrackingId);
        $payload = (strlen($val) > 20) ? ['transaction_id' => $val] : ['tracking_id' => $val];
        return $this->post('/api/developers/personalization_status.php', $payload);
    }

    /**
     * BVN Retrieval by Phone - Submit Request
     */
    public function submitBvnRetrieval($phone, $firstName, $lastName) {
        return $this->post('/api/developers/bvn_retrieval.php', [
            'phone'      => trim($phone),
            'first_name' => trim($firstName),
            'last_name'  => trim($lastName),
        ]);
    }

    /**
     * BVN Retrieval - Check Status
     */
    public function checkBvnRetrievalStatus($transactionIdOrPhone) {
        $val = trim($transactionIdOrPhone);
        $payload = (ctype_digit($val) && strlen($val) === 11 && strpos($val, '0') === 0) ? ['phone' => $val] : ['transaction_id' => $val];
        return $this->post('/api/developers/bvn_retrieval_status.php', $payload);
    }
}
