<?php

class BVN extends ApiAccess{

    public function verifyMyBVN($body, $networkDetails) {
        $bvn = $body->phone;
        $slip = $body->slip ?? '';

        $dvResult = $this->tryDataVerify($bvn);

        if ($dvResult !== null) {
            return $this->processDataVerifyResponse($dvResult, $body, $bvn, $slip);
        }

        return $this->verifyViaAspget($body, $bvn, $slip);
    }

    private function tryDataVerify($bvn) {
        try {
            require_once __DIR__ . '/../../api/providers/dataverify.php';
            $dv = new DataVerifyProvider;
            $details = $this->model->getApiDetails();
            $apiKey = '';
            foreach ($details as $d) {
                if ($d['name'] === 'dataVerifyApi') { $apiKey = $d['value']; break; }
            }
            $dv->apiKey = $apiKey ?: 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM';
            $result = $dv->verifyBVN($bvn);

            $dvStatus = $result['status'] ?? '';
            if ($dvStatus === 'success' || $dvStatus === true) {
                return $result;
            }
        } catch (\Throwable $e) {
            error_log("DataVerify BVN failed: " . $e->getMessage());
        }
        return null;
    }

    private function processDataVerifyResponse($result, $body, $bvn, $slip) {
        $response["status"] = "success";
        $userData = $result['user_data'] ?? $result['data'] ?? $result;
        $placeholder = $userData['first_name'] ?? $userData['firstName'] ?? $bvn;
        $response2 = json_encode($userData);
        $this->model->recordReport($body->userID, $body->ref, $placeholder, $bvn, $response2, $slip, 'YET');
        $this->generatePdf($body->ref, $bvn);
        return $response;
    }

    private function verifyViaAspget($body, $bvn, $slip) {
        $load = json_encode(["bvn" => $bvn, "consent" => true]);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('ASPGET_BVN_URL') ?: 'https://api.aspget.com/bvn/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $load,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Bearer " . (getenv('ASPGET_API_KEY') ?: 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7')
            ),
        ));

        $exereq = curl_exec($curl);
        file_put_contents("pnv.txt", $exereq);
        $err = curl_error($curl);

        if ($err) {
            $response["status"] = "fail";
            $response["msg"] = "Server Connection Error";
            file_put_contents("airtime_error_log2.txt", json_encode($response) . $err);
            curl_close($curl);
            return $response;
        }

        $result = json_decode($exereq);
        curl_close($curl);

        if ($result->status == true || $result->status == 'success') {
            $response["status"] = "success";
            $placeholder = $result->data->firstName;
            $response2 = json_encode($result->data);
            $this->model->recordReport($body->userID, $body->ref, $placeholder, $bvn, $response2, $slip, 'YET');
            $this->generatePdf($body->ref, $bvn);
        } elseif ($result->Status == 'processing' || $result->Status == 'process') {
            $response["status"] = "processing";
            file_put_contents("airtime_processing_log.txt", json_encode($result));
        } elseif ($result->status == false || $result->status == 'fail') {
            $response["status"] = "fail";
            $response["msg"] = "Unable to validate BVN number";
            file_put_contents("bvnfailed.txt", json_encode($result));
        } else {
            $response["status"] = "processing";
            file_put_contents("airtime_processing_log.txt", json_encode($result));
        }

        return $response;
    }

    private function generatePdf($ref, $bvn) {
        // Use standard PDO connection
        $db = $this->connect();
        
        // NOTE: PDF generation currently relies on external service webtopdf.com 
        // and hardcoded URLs. In production, update the domain and PDF generation service.
        
        // Mocking PDF path update for now since the external API will fail locally
        $pdfURL = "https://yourdomain.com/slips/bvn/" . $ref . ".pdf";
        
        $stmt = $db->prepare("UPDATE reports SET pdf = ? WHERE transid = ?");
        $stmt->execute([$pdfURL, $ref]);
    }
}
