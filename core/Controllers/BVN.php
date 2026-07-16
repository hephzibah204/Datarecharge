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
            CURLOPT_URL => 'https://api.aspget.com/bvn/',
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
                "Authorization: Bearer lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7"
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
        $conn = mysqli_connect("localhost", "keytopup_vc", "keytopup_vc", "keytopup_vc");

        $url = "https://webtopdf.com/Controllers/Convert.ashx";
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
        ));
        $uslip = "https://keytopup.com.ng/slips/bvn/?reportID=" . $ref . "&preview=1";
        $data = '{
            "filepath":"' . $uslip . '",
            "pagesize":"A4",
            "width":"",
            "height":"",
            "landscape":"false",
            "leftmargin":"12",
            "topmargin":"12",
            "rightmargin":"12",
            "bottommargin":"14",
            "htmlzoom":"100",
            "header":"",
            "footer":"",
            "pw":"",
            "permissions":"011",
            "type":"PDF",
            "useprintmedia":"true",
            "noscript":"false",
            "nolink":"false",
            "pagenumber":"false",
            "grayscale":"false",
            "bookmark":"false",
            "minloadwaittime":"8",
            "wmtext":"",
            "wmfonttype":"0",
            "wmfontsize":"14",
            "wmfontbold":"false",
            "wmfontitalic":"false",
            "wmfontcolor":"000000",
            "wmprefixtype":"0",
            "wmopacity":"100",
            "wmrotationtype":"0",
            "wmbkmode":"0",
            "curUrl":"/",
            "zipmode":"0",
            "convertemode":"00"
        }';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $resp = curl_exec($curl);
        curl_close($curl);
        $respJ = json_decode($resp);
        $pdfName = $ref . '.pdf';
        if ($respJ->convertedFilePath) {
            $ch = curl_init();
            $dlUrl = "https://webtopdf.com/RESULT/" . $respJ->convertedFilePath;
            curl_setopt($ch, CURLOPT_URL, $dlUrl);
            $fp = fopen("../../slips/bvn/" . $pdfName, 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            $pdfURL = "https://keytopup.com.ng/slips/bvn/" . $pdfName;
            mysqli_query($conn, "UPDATE reports SET pdf = '$pdfURL' WHERE transid = '$ref'");
        }
    }
}
