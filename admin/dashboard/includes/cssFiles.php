<!-- Bootstrap 4 -->
<link rel="stylesheet" href="<?php echo $assetsLoc; ?>/vendor_components/bootstrap/dist/css/bootstrap.min.css">

<!-- DataTables (only on table pages) -->
<?php if (!empty($needsDataTable)): ?>
<link rel="stylesheet" href="<?php echo $assetsLoc; ?>/vendor_components/datatable/datatables.min.css"/>
<?php endif; ?>

<!-- SweetAlert (only on pages that use swal dialogs) -->
<?php if (!empty($needsSweetAlert)): ?>
<link rel="stylesheet" href="<?php echo $assetsLoc; ?>/vendor_components/sweetalert/sweetalert.css"/>
<?php endif; ?>

<!-- Theme style (contains all icon fonts + DataTables styles) -->
<link rel="stylesheet" href="<?php echo $assetsLoc; ?>/css/master_style.css">

<!-- Skin -->
<link rel="stylesheet" href="<?php echo $assetsLoc; ?>/css/skins/_all-skins.css">

<!-- IE support -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<style>
.sale-icon{border:3px solid #f2f2f2; width:80px; height:80px; margin-bottom:10px; border-radius:35rem;}
</style>
