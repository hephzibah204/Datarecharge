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
if ($report["slip"] != "standard") {
    http_response_code(403);
    die();
}
$reponse = json_decode($report["response"]);

$fname = ($reponse->firstname ?? '') . " " . ($reponse->surname ?? '') . " " . ($reponse->middlename ?? '');
$text = "Fullname: " . trim($fname) . " | NIN: " . ($reponse->nin ?? '');
$qrFile = __DIR__ . "/qr.png";
if (file_exists(__DIR__ . '/phpqrcode/qrlib.php')) {
    include(__DIR__ . '/phpqrcode/qrlib.php');
    QRcode::png($text, $qrFile);
}
?>
<!DOCTYPE html>
<html lang="en" class="">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="">
      <title>Standard Slip</title>
      <script type="module" crossorigin="" src="assets/index.2d9b50be.js.download"></script>
      <link rel="stylesheet" href="assets/index.5c9a8b07.css">
   </head>
   <body class="alt-menu sidebar-noneoverflow" style="">
      <div id="app" data-v-app="">
         <div class="main-container" id="container">
            <div class="overlay"></div>
            <div class="search-overlay"></div>
            <div id="content" class="main-content">
               <div class="layout-px-spacing">
                  <div class="row mt-5">
                     <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 card component-card animate__animated animate__slideInRight animate__slow">
                        <div class="card-body">
                           <div data-v-5d0b81a4="">
                              <div class="overflow-auto mt-3" data-v-5d0b81a4="" style="max-height: 500px;">
                                 <div id="Solo-ID-Container" data-v-5d0b81a4="">
                                    <div class="text-black" data-v-2fe93b25="">
                                       <table border="1" data-v-2fe93b25="">
                                          <tr data-v-2fe93b25="">
                                             <td class="id-bkg-solo" data-v-2fe93b25="">
                                                <table border="0" data-v-2fe93b25="" style="width: 320px; background: none;">
                                                   <tr data-v-2fe93b25="">
                                                      <td colspan="2" class="text-right p-0 px-1 pr-5" data-v-2fe93b25=""><img src="assets/coat-of-arm.a008a049.png" width="60" height="50" data-v-2fe93b25=""></td>
                                                      <td class="text-center text-black" data-v-2fe93b25="">
                                                         <h6 class="mb-0 pt-2" data-v-2fe93b25="" style="font-weight: bold; font-family: Helvetica, sans-serif;"> NGA </h6>
                                                         <span class="nin-watermark nin-watermark-1" data-v-2fe93b25=""><?php echo $reponse->nin ?? ''; ?></span>
                                                      </td>
                                                   </tr>
                                                   <tr data-v-2fe93b25="">
                                                      <td rowspan="3" class="p-0 pr-2" data-v-2fe93b25=""><img src="data:image/png;base64,<?php echo $reponse->photo ?? ''; ?>" width="75" height="100" data-v-2fe93b25=""></td>
                                                      <td rowspan="3" class="txt-info p-0" nowrap="" data-v-2fe93b25="" style="width: 160px !important;">
                                                         <div class="d-flex flex-column" data-v-2fe93b25="">
                                                            <span class="info-title" data-v-2fe93b25="">Surname/Nom</span>
                                                            <div class="text-black font-ocrb font-13 py-0 my-0" data-v-2fe93b25="" style="margin-top: 0px !important;"><?php echo strtoupper($reponse->surname ?? ''); ?></div>
                                                            <span class="info-title pt-0x mt-0x" data-v-2fe93b25="">Given names/Prenoms</span>
                                                            <div class="text-black font-ocrb text-bold" data-v-2fe93b25=""><?php echo strtoupper($reponse->firstname ?? ''); ?>, <?php echo strtoupper($reponse->middlename ?? ''); ?></div>
                                                            <span class="info-title" data-v-2fe93b25="">Date of Birth</span>
                                                            <div class="text-black font-ocrb" data-v-2fe93b25=""><?php echo strtoupper(date('d M Y', strtotime($reponse->birthdate ?? ''))); ?></div>
                                                         </div>
                                                      </td>
                                                      <td rowspan="3" class="text-center p-0" data-v-2fe93b25="">
                                                        <img src="<?php echo file_exists($qrFile) ? 'qr.png' : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='; ?>" width="91" height="91" style="width: 90px; height: 90px;"/>
                                                      </td>
                                                   </tr>
                                                </table>
                                             </td>
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
