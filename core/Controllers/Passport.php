<?php

class Passport extends ApiAccess{

    public function verifyMyPassport($body, $networkDetails){
        $details = $this->model->getApiDetails();
        $apiUrl = $this->getConfigValue($details, 'passportProvider') ?: (getenv('ASPGET_PASSPORT_URL') ?: 'https://api.aspget.com/passport/');
        $apiKey = $this->getConfigValue($details, 'passportApi') ?: (getenv('ASPGET_API_KEY') ?: 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7');
        $status = $this->getConfigValue($details, 'passportStatus');

        if ($status === 'Off') {
            return ["status" => "fail", "msg" => "International Passport service is currently disabled"];
        }

        $load = json_encode([
            "passportNo" => $body->passportNo ?? $body->phone,
            "consent" => true
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $load,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $apiKey"
            ],
        ]);

        $exereq = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            curl_close($curl);
            return ["status" => "fail", "msg" => "Server Connection Error"];
        }

        $result = json_decode($exereq);
        curl_close($curl);

        if ($result->status == true || $result->status == 'success') {
            return ["status" => "success", "data" => $result->data ?? $result];
        } elseif ($result->status == false || $result->status == 'fail') {
            return ["status" => "fail", "msg" => $result->message ?? "Unable to verify Passport"];
        } else {
            return ["status" => "processing", "data" => $result];
        }
    }
}
