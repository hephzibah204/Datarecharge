 <div class="page-content header-clear-medium">
 <div class="card card-style p-3">
     <a href="create-card" class="btn btn-success mb-5">Create New Card</a>
            <?php foreach($data as $list){ if (!empty($data) && !empty($list->card_id)){  ?>
                <a href="virtual-card-details?card_id=<?php echo $list->card_id; ?>" class="d-flex">
                    <div class="align-self-center">
                     <span class="icon icon-s gradient-green color-white rounded-sm shadow-xxl"><i class="fa fa-credit-card font-15"></i></span>
                    </div>
                    <div class="align-self-center">
                        <h6 class="ps-3 font-12 mt-3 color-theme opacity-70"><?php echo $list->brand; ?> **** <?php echo $list->last_four; ?></h6>
                    </div>
                    <div class="ms-auto text-end align-self-center">
                        <h5 class="color-theme font-15 font-700 d-block mb-n1"><?php echo $list->status; ?></h5>
                        <span class="color-green-dark font-10"><?php echo $controller->formatDate2($list->created_at); ?> <i class="fa fa-check-circle"></i></span>
                     </div>
                </a> <?php }  else {echo "<h3 class='text-danger'>You have not create any card yet </h3>";}?>
                <div class="divider my-3"></div>
            
            <?php } ?>
        </div>