<div class="row">


    <div class="col-md-4">
        <div class="box">
            <div class="box-body">
                <div class="d-flex  align-items-center">
                    <h2 class="fa fa-money text-white font-size-20 bg-info" style="border-radius:50px; text-align:center;  padding:10px; width:50px; height:auto;"></h2>
                    <h2 class="mb-2 mt-2 pl-5 ml-5">
                        <a class="" href="<?php echo $urlAddon; ?>transactions"> 
                            <b>All Transactions</b>
                        </a>
                    </h2>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box">
            <div class="box-body">
                <div class="d-flex  align-items-center">
                    <h2 class="fa fa-bar-chart text-white font-size-20 bg-info" style="border-radius:50px; text-align:center;  padding:10px; width:50px; height:auto;"></h2>
                    <h2 class="mb-2 mt-2 pl-5 ml-5">
                        <a class="" href="<?php echo $urlAddon; ?>failed-transactions"> 
                            <b>Failed/Processing</b>
                        </a>
                    </h2>
                    
                </div>
            </div>
        </div>
    </div>

    <?php if(AdminController::$role == 1): ?>
    <div class="col-md-4">
        <div class="box">
            <div class="box-body">
                <div class="d-flex  align-items-center">
                    <h2 class="fa fa-area-chart text-white font-size-20 bg-info" style="border-radius:50px; text-align:center;  padding:10px; width:50px; height:auto;"></h2>
                    <h2 class="mb-2 mt-2 pl-5 ml-5">
                        <a class="" href="<?php echo $urlAddon; ?>sale-analysis"> 
                            <b>Sales Analysis</b>
                        </a>
                    </h2>
                    
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
   <div class="col-md-4">
        <div class="box">
            <div class="box-body">
                <div class="d-flex  align-items-center">
                    <h2 class="fa fa-book text-white font-size-20 bg-info" style="border-radius:50px; text-align:center;  padding:10px; width:50px; height:auto;"></h2>
                    <h2 class="mb-2 mt-2 pl-5 ml-5">
                        <a class="" href="<?php echo $urlAddon; ?>data-sale-analysis"> 
                            <b>Data Sale Analysis</b>
                        </a>
                    </h2>
                    
                </div>
            </div>
        </div>
    </div>
    
   <div class="col-md-4">
        <div class="box">
            <div class="box-body">
                <div class="d-flex  align-items-center">
                    <h2 class="fa fa-book text-white font-size-20 bg-info" style="border-radius:50px; text-align:center;  padding:10px; width:50px; height:auto;"></h2>
                    <h2 class="mb-2 mt-2 pl-5 ml-5">
                        <a class="" href="<?php echo $urlAddon; ?>sales-by-user"> 
                            <b>Sales By User</b>
                        </a>
                    </h2>
                    
                </div>
            </div>
        </div>
    </div>

</div>
