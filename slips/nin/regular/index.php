<?php
$dbPath = __DIR__ . '/../../../database/providers.db';
try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    die();
}

$reportId = $_GET["reportID"] ?? '';
$reportId = filter_var($reportId, FILTER_UNSAFE_RAW);

$query = $db->prepare("SELECT * FROM reports WHERE transid = :id");
$query->bindValue(':id', $reportId, PDO::PARAM_STR);
$query->execute();
$report = $query->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    http_response_code(404);
    die();
}
if ($report["slip"] != "regular") {
    http_response_code(403);
    die();
}
$reponse = json_decode($report["response"]);
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Regular Slip</title>
      <script type="module" crossorigin="" src="assets/index.fb56c225.js.download"></script>
      <link rel="stylesheet" href="assets/index.c1dd1ce4.css">
   </head>
   <body class="alt-menu sidebar-noneoverflow" style="">
      <div id="app" data-v-app="">
         <div class="main-container" id="container">
            <div id="content" class="main-content">
               <div class="layout-px-spacing">
                  <div class="row mt-5">
                     <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 card component-card animate__animated animate__slideInDown animate__slow">
                        <div class="card-body">
                           <div data-v-97730256="">
                              <div class="mt-3" data-v-97730256="" style="max-height: 500px;">
                                 <div id="NIMC-Slip-Container" data-v-97730256="">
                                    <div class="my-5 text-black" data-v-e273c7d6="">
                                       <table border="2" class="border-right-thick border-left-thick border-top-thick border-bottom-thick" data-v-e273c7d6="" style="width: 100%;">
                                          <tr data-v-e273c7d6="">
                                             <td colspan="6" class="text-center" data-v-e273c7d6=""><img src="assets/id-header-nimc-slip.33474f14.jpg" width="750" height="70" data-v-e273c7d6=""></td>
                                          </tr>
                                          <tr data-v-e273c7d6="">
                                             <th class="width-100 border-right-none border-top-none" data-v-e273c7d6="">Tracking ID: </th>
                                             <td class="border-left-none border-top-none border-right-thick" data-v-e273c7d6=""><?php echo $reponse->trackingId ?? ''; ?></td>
                                             <th class="border-top-none border-bottom-none border-left-thick border-right-none" nowrap="" data-v-e273c7d6=""> Surname: </th>
                                             <td class="border-left-none border-top-none border-bottom-none" data-v-e273c7d6=""><?php echo $reponse->surname ?? ''; ?></td>
                                             <td rowspan="2" class="border-none" data-v-e273c7d6=""><b data-v-e273c7d6="">Address:</b> <br data-v-e273c7d6=""> <?php echo $reponse->residence_address ?? 'N/A'; ?></td>
                                             <td rowspan="4" class="border-left-none border-bottom-none border-top-none" data-v-e273c7d6=""><img src="<?php if(substr($reponse->photo ?? '',0,4) == "data"){ echo $reponse->photo; } else { echo "data:image/png;base64,".($reponse->photo ?? ''); } ?>"  height="150" width="120" class="rounded" data-v-e273c7d6=""></td>
                                          </tr>
                                          <tr data-v-e273c7d6="">
                                             <th class="width-100 border-top-none border-right-none border-bottom-none" data-v-e273c7d6="">NIN: </th>
                                             <td class="border-none" data-v-e273c7d6=""><?php echo $reponse->nin ?? ''; ?></td>
                                             <th class="border-right-none border-bottom-none" nowrap="" data-v-e273c7d6="">First Name:</th>
                                             <td class="border-left-none border-bottom-none" data-v-e273c7d6=""><?php echo $reponse->firstname ?? ''; ?></td>
                                          </tr>
                                          <tr data-v-e273c7d6="">
                                             <td colspan="2" class="border-bottom-none border-right-none" data-v-e273c7d6=""></td>
                                             <th class="border-right-none border-bottom-none" nowrap="" data-v-e273c7d6=""> Middle Name: </th>
                                             <td class="border-left-none border-bottom-none" data-v-e273c7d6=""><?php echo $reponse->middlename ?? ''; ?></td>
                                             <td class="border-none" data-v-e273c7d6=""><?php echo $reponse->residence_lga ?? ''; ?></td>
                                          </tr>
                                          <tr data-v-e273c7d6="">
                                             <td class="width-100 border-top-none border-right-none border-bottom-none" colspan="2" data-v-e273c7d6=""></td>
                                             <th class="border-right-none border-bottom-none" data-v-e273c7d6="">Gender:</th>
                                             <td class="border-left-none border-bottom-none" data-v-e273c7d6=""><?php echo $reponse->gender ?? ''; ?></td>
                                             <td class="border-none" data-v-e273c7d6=""><?php echo $reponse->residence_state ?? ''; ?></td>
                                          </tr>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
