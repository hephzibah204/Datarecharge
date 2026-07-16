	<div class="row">


        <div class="col-md-6">
	        <div class="box">
	            <div class="box-body">
	                <div class="d-flex align-items-center justify-content-between">
	                    <div class="">
	                        <h2 class="mb-2">N<?php echo $data[1]["walletTotal"]; ?></h2>
	                        <p class="text-muted mb-0"><span class="badge badge-primary">Total Api Wallet</span></p>
	                    </div>
	                    <div class="fa fa-user text-primary font-size-30"></div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-md-6">
	        <div class="box">
	            <div class="box-body">
	                <div class="d-flex align-items-center justify-content-between">
	                    <div class="">
	                        <h2 class="mb-2">N<?php echo $data[0]["userWalletTotal"]; ?></h2>
	                        <p class="text-muted mb-0"><span class="badge badge-primary">Total Users Wallet</span></p>
	                    </div>
	                    <div class="fa fa-users text-primary font-size-30"></div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-12">
	        <div class="box">
	            <div class="row no-gutters py-2">
	                <div class="col-12 col-lg-4">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-chain text-info font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-info my-0 text-right">N<?php echo $data[1]["walletOneBalance"]; ?></h2>
	                                <p class="mb-0 text-muted text-right">Wallet Balance</p>
	                                <p class="mb-0 text-dark text-right">(<b><?php echo $data[1]["walletOneProvider"]; ?></b>)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="col-12 col-lg-4">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-chain text-info font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-info my-0 text-right">N<?php echo $data[1]["walletTwoBalance"]; ?> </h2>
	                                <p class="mb-0 text-muted text-right">Wallet Balance</p>
	                                <p class="mb-0 text-dark text-right">(<b><?php echo $data[1]["walletTwoProvider"]; ?></b>)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="col-12 col-lg-4">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-chain text-info font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-info my-0 text-right">N<?php echo $data[1]["walletThreeBalance"]; ?> </h2>
	                                <p class="mb-0 text-muted text-right">Wallet Balance</p>
	                                <p class="mb-0 text-dark text-right">(<b><?php echo $data[1]["walletThreeProvider"]; ?></b>)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	            </div>
	        </div>
	    </div>

	    <div class="col-12">
	        <div class="box">
	            <div class="row no-gutters py-2">

	                <div class="col-12 col-lg-3">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-money text-success font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-success my-0 text-right">N<?php echo number_format($data[0]["uwCount"]); ?> </h2>
	                                <p class="mb-0 text-muted text-right">User</p>
	                                <p class="mb-0 text-dark text-right">(Wallet)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="col-12 col-lg-3">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-money text-success font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-success my-0 text-right">N<?php echo number_format($data[0]["awCount"]); ?></h2>
	                                <p class="mb-0 text-muted text-right">Agent</p>
	                                <p class="mb-0 text-dark text-right">(Wallet)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="col-12 col-lg-3">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-money text-success font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-success my-0 text-right">N<?php echo number_format($data[0]["vwCount"]); ?></h2>
	                                <p class="mb-0 text-muted text-right">Vendors</p>
	                                <p class="mb-0 text-dark text-right">(Wallet)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="col-12 col-lg-3">
	                    <div class="box-body br-1 border-light no-radius">
	                        <div class="d-flex align-items-center justify-content-between">
	                            <div>
	                                <i class="fa fa-money text-success font-size-50"></i><br>
	                            </div>
	                            <div>
	                                <h2 class="text-success my-0 text-right">N<?php echo number_format($data[0]["rwCount"]); ?></h2>
	                                <p class="mb-0 text-muted text-right">Referrals</p>
	                                <p class="mb-0 text-dark text-right">(Wallet)</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	            </div>
	        </div>
	    </div>

	    

	    

	</div>
	