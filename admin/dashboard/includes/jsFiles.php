<!-- jQuery 3 -->
<script src="<?php echo $assetsLoc; ?>/vendor_components/jquery-3.3.1/jquery-3.3.1.min.js"></script>

<!-- Popper + Bootstrap 4 -->
<script src="<?php echo $assetsLoc; ?>/vendor_components/popper/dist/popper.min.js"></script>
<script src="<?php echo $assetsLoc; ?>/vendor_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Slimscroll -->
<script src="<?php echo $assetsLoc; ?>/vendor_components/jquery-slimscroll/jquery.slimscroll.js"></script>

<!-- FastClick -->
<script src="<?php echo $assetsLoc; ?>/vendor_components/fastclick/lib/fastclick.js"></script>

<!-- DataTables (only on table pages) -->
<?php if (!empty($needsDataTable)): ?>
<script src="<?php echo $assetsLoc; ?>/vendor_components/datatable/datatables.min.js"></script>
<script src="<?php echo $assetsLoc; ?>/js/pages/data-table.js"></script>
<?php endif; ?>

<!-- SweetAlert (only on pages that use swal dialogs) -->
<?php if (!empty($needsSweetAlert)): ?>
<script src="<?php echo $assetsLoc; ?>/vendor_components/sweetalert/sweetalert.min.js"></script>
<?php endif; ?>

<!-- ApexCharts (only on sales-by-user) -->
<?php if (!empty($needsChart)): ?>
<script src="<?php echo $assetsLoc; ?>/vendor_components/apexcharts-bundle/irregular-data-series.js"></script>
<script src="<?php echo $assetsLoc; ?>/vendor_components/apexcharts-bundle/dist/apexcharts.js"></script>
<?php endif; ?>

<!-- Template -->
<script src="<?php echo $assetsLoc; ?>/js/template.js"></script>
