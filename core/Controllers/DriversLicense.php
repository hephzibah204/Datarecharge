<?php

class DriversLicense extends ApiAccess{

    public function verifyMyLicense($body, $networkDetails){
        $details = $this->model->getApiDetails();
        $apiUrl = $this->getConfigValue($details, 'dlProvider') ?: 'https://api.aspget.com/drivers-license/';
        $apiKey = $this->getConfigValue($details, 'dlApi') ?: 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7';
        $status = $this->getConfigValue($details, 'dlStatus');

        if ($status === 'Off') {
            return ["status" => "fail", "msg" => "Driver's License service is currently disabled"];
        }

        $load = json_encode([
            "licenseNo" => $body->licenseNo ?? $body->phone,
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
            return ["status" => "fail", "msg" => $result->message ?? "Unable to verify Driver's License"];
        } else {
            return ["status" => "processing", "data" => $result];
        }
    }
}
