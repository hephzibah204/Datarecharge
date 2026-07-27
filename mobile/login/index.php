<?php 


    $_GET["settings"]="YES";
    
    require_once("../home/includes/route.php");

    $design = $data->logindesign;
    $color = $data->sitecolor;
    $name = $data->sitename;

    $allowedDesigns = ['1', '2', '3']; // Add valid designs here
    $design = in_array((string)$design, $allowedDesigns) ? $design : '1';
    include("login".$design.".php");

?>