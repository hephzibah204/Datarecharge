<?php

    class NIN extends ApiAccess{

        public function verifyMyNIN($body,$networkDetails){
            $details=$this->model->getApiDetails();
            $apiUrl = $this->getConfigValue($details, 'ninProvider') ?: 'https://ambverify.com.ng/api/v1';
            $apiKey = $this->getConfigValue($details, 'ninApi') ?: '';
            $ninStatus = $this->getConfigValue($details, 'ninStatus');

            if ($ninStatus === 'Off') {
                return ["status" => "fail", "msg" => "NIN service is currently disabled"];
            }

            $nin = $body->phone;
            $validationType = $body->validation_type ?? 'Modification';

            $load = json_encode([
                "nins" => [$nin],
                "validation_type" => $validationType
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl . '/nin_validation.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $load,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    "Authorization: Bearer $apiKey"
                ),
            ));

            $exereq = curl_exec($curl);
            $err = curl_error($curl);

            if($err){
                $response["status"] = "fail";
                $response["msg"] = "Server Connection Error";
                file_put_contents("airtime_error_log2.txt", json_encode($response).$err);
                curl_close($curl);
                return $response;
            }

            $result = json_decode($exereq);
            curl_close($curl);

            if (isset($result->success) && $result->success == true) {
                $response["status"] = "success";
                $response["msg"] = "NIN validation submitted successfully";
                $response["data"] = $result;
                $response["batch_id"] = $result->batch_id ?? '';

                $this->model->recordReport(
                    $body->userID,
                    $body->ref,
                    $result->items[0]->nin ?? $nin,
                    $result->items[0]->nin ?? $nin,
                    json_encode($result),
                    $body->slip ?? '',
                    'Processing'
                );
            } elseif (isset($result->success) && $result->success == false) {
                $response["status"] = "fail";
                $response["msg"] = $result->error ?? "Unable to validate NIN number";
                file_put_contents("nin_failed.txt", json_encode($result));
            } else {
                $response["status"] = "processing";
                $response["msg"] = "NIN validation is being processed";
                file_put_contents("airtime_processing_log.txt", json_encode($result));
            }

            return $response;
        }

        public function submitIPE($trackingIds, $ref = '') {
            $details = $this->model->getApiDetails();
            $apiUrl = $this->getConfigValue($details, 'ninProvider') ?: 'https://ambverify.com.ng/api/v1';
            $apiKey = $this->getConfigValue($details, 'ninApi') ?: '';

            if (!is_array($trackingIds)) $trackingIds = [$trackingIds];

            $load = json_encode(["tracking_ids" => $trackingIds]);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl . '/ipe_clearance.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $load,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    "Authorization: Bearer $apiKey"
                ),
            ));

            $exereq = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return ["status" => "fail", "msg" => "IPE submission failed: $err"];
            }

            $result = json_decode($exereq);
            if (isset($result->success) && $result->success == true) {
                return [
                    "status" => "success",
                    "msg" => "IPE clearance submitted",
                    "data" => $result,
                    "batch_id" => $result->batch_id ?? ''
                ];
            }

            return ["status" => "fail", "msg" => $result->error ?? "IPE submission failed"];
        }

        public function checkAmbVerifyStatus($params = []) {
            $details = $this->model->getApiDetails();
            $apiUrl = $this->getConfigValue($details, 'ninProvider') ?: 'https://ambverify.com.ng/api/v1';
            $apiKey = $this->getConfigValue($details, 'ninApi') ?: '';

            $query = http_build_query($params);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl . '/check_status.php?' . $query,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    "Authorization: Bearer $apiKey"
                ),
            ));

            $exereq = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) return ["status" => "fail", "msg" => $err];
            return json_decode($exereq, true);
        }
    }
