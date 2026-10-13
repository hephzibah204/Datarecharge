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

            $nin = $body->phone ?? $body->nin ?? '';
            $validationType = $body->validation_type ?? 'no_record_found';

            // Map and normalize to the 5 supported DataVerify validation types
            $typeMap = [
                'no_record_found'           => 'no_record_found',
                'no record found'           => 'no_record_found',
                'standard'                  => 'no_record_found',
                'sim_validation'            => 'sim_validation',
                'sim validation'            => 'sim_validation',
                'premium'                   => 'sim_validation',
                'bank_validation'           => 'bank_validation',
                'bank validation'           => 'bank_validation',
                'modification_validation'   => 'modification_validation',
                'modification validation'   => 'modification_validation',
                'modification'              => 'modification_validation',
                'photographic_error'        => 'photographic_error',
                'photographic error'        => 'photographic_error',
            ];
            $normalizedType = $typeMap[strtolower(trim($validationType))] ?? $validationType;

            // Route to DataVerify if configured or fallback
            $dataVerifyApiKey = $this->getConfigValue($details, 'dataVerifyApi') ?: getenv('PROVIDER_DataVerify_API_KEY');
            $dataVerifyEndpoint = $this->getConfigValue($details, 'dataVerifyProvider') ?: 'https://dataverify.org';

            if (strpos($apiUrl, 'dataverify') !== false || !empty($body->use_dataverify)) {
                $dvKey = $apiKey ?: ($dataVerifyApiKey ?: 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM');
                $isSlip = !empty($body->slip) || in_array(strtolower($body->network ?? ''), ['regular', 'standard', 'premium']);

                if ($isSlip) {
                    $dvUrl = rtrim($dataVerifyEndpoint, '/') . '/developers/nin_slips/nin_premium.php';
                    $load = json_encode([
                        "api_key" => $dvKey,
                        "nin"     => $nin
                    ]);
                } else {
                    $dvUrl = rtrim($dataVerifyEndpoint, '/') . '/api/developers/validation.php';
                    $load = json_encode([
                        "api_key"         => $dvKey,
                        "nin"             => $nin,
                        "validation_type" => $normalizedType
                    ]);
                }

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $dvUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => $load,
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $dvKey,
                        'X-API-Key: ' . $dvKey
                    ],
                    CURLOPT_TIMEOUT        => 35,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $exereq = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    return ["status" => "fail", "msg" => "DataVerify connection error: $err"];
                }
                $result = json_decode($exereq);
                if (isset($result->status) && ($result->status === true || $result->status === 'success' || !empty($result->user_data))) {
                    if ($isSlip && !empty($body->userID)) {
                        $userData = $result->user_data ?? $result;
                        $placeholder = $userData->firstName ?? ($userData->first_name ?? $nin);
                        $this->model->recordReport(
                            $body->userID,
                            $body->ref ?? ('NIN_' . time()),
                            $placeholder,
                            $nin,
                            json_encode($userData),
                            $body->slip ?? ($body->network ?? 'regular'),
                            'Processing'
                        );
                    }
                    return [
                        "status"          => "success",
                        "msg"             => $result->message ?? ($isSlip ? "NIN verification completed successfully" : "NIN validation submitted successfully"),
                        "data"            => $result,
                        "transaction_id"  => $result->transaction_id ?? '',
                        "request_status"  => $result->request_status ?? ($isSlip ? 'completed' : 'pending'),
                        "validation_type" => $normalizedType
                    ];
                }
                return [
                    "status" => "fail",
                    "msg"    => $result->message ?? $result->error ?? "Unable to process NIN request",
                    "data"   => $result
                ];
            }

            // Default AmbVerify / standard endpoint
            $load = json_encode([
                "nins" => [$nin],
                "validation_type" => $normalizedType
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

            if (strpos($apiUrl, 'dataverify') !== false) {
                require_once __DIR__ . '/../../api/providers/dataverify.php';
                $dv = new DataVerifyProvider();
                $dv->apiKey = $apiKey ?: ($this->getConfigValue($details, 'dataVerifyApi') ?: 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM');
                $dv->endpoint = $this->getConfigValue($details, 'dataVerifyProvider') ?: 'https://dataverify.org';

                $results = [];
                $firstRes = null;
                foreach ($trackingIds as $tid) {
                    $res = $dv->ipeClearance($tid);
                    $results[] = $res;
                    if (!$firstRes) $firstRes = $res;
                }

                $ok = !empty($firstRes['status']) && ($firstRes['status'] === true || $firstRes['status'] === 'success');
                if ($ok) {
                    return [
                        "status"   => "success",
                        "msg"      => $firstRes['message'] ?? "IPE clearance submitted successfully",
                        "data"     => count($results) === 1 ? $firstRes : $results,
                        "batch_id" => $firstRes['transaction_id'] ?? ''
                    ];
                }
                return [
                    "status" => "fail",
                    "msg"    => $firstRes['message'] ?? "IPE clearance submission failed",
                    "data"   => $results
                ];
            }

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
