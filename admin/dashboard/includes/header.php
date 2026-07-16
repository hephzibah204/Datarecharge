<header class="main-header">	
	  <div class="p-10 clearfix float-left">
		<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
			<i class="ti-align-left"></i>
		</a>
		<!-- Logo -->
		<a href="dashboard" class="logo" >
		  <!-- logo-->
		  <span class="logo-lg">
			  <img src="<?php echo $assetsLoc; ?>/img/logodark0.png?v=1" alt="logo" class="dark-logo" width="80">
		  </span>
		</a>	  
	  </div>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <div>
		  <a id="toggle_res_search" data-toggle="collapse" data-target="#search_form" class="res-only-view" href="javascript:void(0);"><i class="mdi mdi-magnify"></i></a>
		  <form id="search_form" role="search" class="top-nav-search pull-left collapse">
			<div class="input-group">
				<span class="input-group-btn">
				<button type="button" class="btn  btn-default" data-target="#search_form" data-toggle="collapse" aria-label="Close" aria-expanded="true"><i class="mdi mdi-magnify"></i></button>
				</span>
				<input type="text" name="" class="form-control" placeholder="Search in app...">
			</div>
		  </form> 
		
	  </div>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">		  
          
          <?php if(AdminController::$role == 1): ?>
            <li class="dropdown messages-menu">
              <a href="<?php echo $urlAddon; ?>wallet-balance" class="dropdown-toggle">
                <i class="fa fa-money"></i>
              </a>
            </li>

            <li class="dropdown messages-menu">
              <a href="<?php echo $urlAddon; ?>configurations" class="dropdown-toggle">
                <i class="fa fa-plug"></i>
              </a>
            </li>
          <?php endif; ?>

          <li class="dropdown messages-menu">
            <a href="<?php echo $urlAddon; ?>credit-user-account" class="dropdown-toggle">
              <i class="fa fa-user-plus"></i>
            </a>
          </li>

		  <!-- User Account-->
          <li class="dropdown user user-menu messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="mdi mdi-account"></i>
            </a>
            <ul class="dropdown-menu scale-up">
             
              <li class="user-body">
                <div class="row no-gutters">
                  <div class="col-12 text-left">
                    <a href="<?php echo $urlAddon; ?>manage-account"><i class="ion ion-person"></i> My Profile</a>
                  </div>
                  <div role="separator" class="divider col-12"></div>
				          <div class="col-12 text-left">
                    <a href="<?php echo $urlAddon; ?>logout"><i class="fa fa-power-off"></i> Logout</a>
                  </div>				
                </div>
                
              </li>
            </ul>
          </li>	
          
        </ul>
      </div>
    </nav>
  </header>