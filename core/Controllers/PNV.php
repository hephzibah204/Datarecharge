<?php

    class PNV extends ApiAccess{

        //Purchase Airtime
		public function verifyMyPNV($body,$networkDetails){

            $details=$this->model->getApiDetails();
 
          
            //Get Api Key Details
            //$host = self::getConfigValue($details,$networkname.$name."Provider");
            //$apiKey = self::getConfigValue($details,$networkname.$name."Key");
            $slip = $body->slip;
            $nin = $body->phone;
            
            $load = json_encode(
	            array(
	                "phoneNumber" => $body->phone,
					"consent" => true
				));
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('ASPGET_PNV_URL') ?: 'https://api.aspget.com/pnv/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$load,
             
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Bearer " . (getenv('ASPGET_API_KEY') ?: 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7')
            ),
            ));

            $exereq = curl_exec($curl);
            file_put_contents("pnv.txt",$exereq);
            $err = curl_error($curl);
   
            if($err){
                $response["status"] = "fail";
                $response["msg"] = "Server Connection Error";
                file_put_contents("airtime_error_log2.txt",json_encode($response).$err);
                curl_close($curl);
                return $response;
            }

            $result=json_decode($exereq);
            curl_close($curl);

            if($result->status==true || $result->status=='success'){
                $response["status"] = "success";
                $result->data->firstname = $result->data->firstName;
                $result->data->middlename = $result->data->middleName;
                $result->data->surname = $result->data->lastName;
                $result->data->telephoneno = $result->data->phoneNumber;
                $result->data->birthdate = $result->data->dob;
                $result->data->residence_state = $result->data->state;
                $result->data->residence_town = $result->data->town;
                $result->data->residence_address = $result->data->addressLine;
                $result->data->residence_lga = $result->data->lga;
                $result->data->birthcountry = $result->data->birthCountry;
                $result->data->birthstate = $result->data->birthState;
                $result->data->birthlga = $result->data->birthLGA;
                $result->data->country = $result->data->country;
                $result->data->title = $result->data->title;
                $result->data->photo = str_replace("data:image\/jpg;base64,","",$result->data->photo);
                $placeholder = $result->data->firstname;
                $response2 = json_encode($result->data);
                $this->model->recordReport($body->userID,$body->ref,$placeholder,$nin,$response2,$slip,'YET');
                
                $conn = mysqli_connect("localhost","keytopup_vc","keytopup_vc","keytopup_vc");   
                
                $url = "https://webtopdf.com/Controllers/Convert.ashx";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $headers = array(
                   "Content-Type: application/json",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $uslip = "https://keytopup.com.ng/slips/nin/{$slip}/?reportID=".$body->ref."&preview=1";
                $data = '
                {
                    "filepath":"'.$uslip.'",
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
                //die($resp);
                curl_close($curl);
                $respJ = json_decode($resp);
                $pdfName = $body->ref.'.pdf';
                if($respJ->convertedFilePath){
                    $ch = curl_init();
                    $url = "https://webtopdf.com/RESULT/".$respJ->convertedFilePath;
                    curl_setopt($ch, CURLOPT_URL,$url);
                    $fp = fopen("../../slips/nin/{$slip}/".$pdfName, 'w+');
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_exec ($ch);
                    curl_close ($ch);
                    fclose($fp);
                    $pdfURL = "https://keytopup.com.ng/slips/nin/{$slip}/".$pdfName;
                    mysqli_query($conn, "UPDATE reports SET pdf = '$pdfURL' WHERE transid = '$body->ref'");
                }
            }
            elseif($result->Status=='processing' || $result->Status=='process'){
                $response["status"] = "processing";
                file_put_contents("airtime_processing_log.txt",json_encode($result));
            }
            elseif($result->status==false || $result->status=='fail'){
                $response["status"] = "fail";
                $response["msg"] = "Unable to validate NIN number";
                file_put_contents("nin_failed.txt",json_encode($result));
            }
            else{
                $response["status"] = "processing";
                file_put_contents("airtime_processing_log.txt",json_encode($result));
            }

            return $response;
		}

     
    }

?>