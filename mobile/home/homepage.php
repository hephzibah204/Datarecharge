<?php 

    /*
        Topupmate Technology LTD

        Dear Developer, Please Note That This Is A Licensed VTU Script 
        And Is Not To Be Used Without Full Permission 

        If found guilty of bypassing or violating our terms of service, 
        
        1. Your website would be blocked
        2. Your database would be permanently deleted
        3. Legal actions would be taken agents you
        4. You would be required to pay a fine of N250K

        From Topupmate Technology.
        Website: www.topupmate.com
        Email: support@topupmate.com
        PWhatsapp: 07032529431
    */

    
    $design = (is_object($data3) && isset($data3->homedesign)) ? $data3->homedesign : '6';
    $color  = (is_object($data3) && isset($data3->sitecolor))  ? $data3->sitecolor  : '#085406';
    $name   = (is_object($data3) && isset($data3->sitename))   ? $data3->sitename   : 'DataRecharge';

    $allowedDesigns = ['1', '2', '3', '4', '5', '6']; // Add valid designs here
    $design = in_array((string)$design, $allowedDesigns) ? $design : '6';
    $pageFile = "homepages/homepage".$design.".php";
    include(file_exists($pageFile) ? $pageFile : "homepages/homepage6.php");

?>