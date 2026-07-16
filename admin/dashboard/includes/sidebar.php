<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar">
		
      <!-- sidebar menu-->
      <ul class="sidebar-menu" data-widget="tree">
        <li>
          <a href="<?php echo $urlAddon; ?>dashboard">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>community">
            <i class="fa fa-cubes"></i> <span>Community</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <?php if(AdminController::$role == 1): ?>

        <li>
          <a href="<?php echo $urlAddon; ?>wallet-balance">
            <i class="fa fa-money"></i> <span>API Wallet</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>
        
        <li>
          <a href="<?php echo $urlAddon; ?>system-users">
            <i class="fa fa-user-secret"></i> <span>System Users</span>
		      	<span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>
        <?php endif; ?>

        <li>
          <a href="<?php echo $urlAddon; ?>subscribers">
            <i class="fa fa-users"></i> <span>Customers</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>credit-user-account">
            <i class="fa fa-user-plus"></i> <span>Credit User</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>all-services">
            <i class="fa fa-list"></i> <span>Services</span>
		      	<span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>exam-pin">
            <i class="fa fa-graduation-cap"></i> <span>Exam Pins</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>providers">
            <i class="fa fa-plug"></i> <span>Providers</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>ni-modifications">
            <i class="fa fa-id-card"></i> <span>NIN Modifications</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>nin-fee-settings">
            <i class="fa fa-money"></i> <span>NIN Fee Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>nin-slip-pricing">
            <i class="fa fa-tags"></i> <span>NIN Slip Pricing</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>transactions-list">
            <i class="fa fa-money"></i> <span>Transactions</span>
		      	<span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

       
        <li>
          <a href="<?php echo $urlAddon; ?>messaging">
            <i class="fa fa-wechat"></i> <span>Messaging</span>
		      	<span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        
        <?php if(AdminController::$role == 1): ?>
        
        <li>
          <a href="<?php echo $urlAddon; ?>configurations">
            <i class="fa fa-cog"></i> <span>Configurations</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <?php endif; ?>

        <li>
          <a href="<?php echo $urlAddon; ?>manage-account">
            <i class="fa fa-user"></i> <span>Manage Account</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>logout">
            <i class="fa fa-lock"></i> <span>Logout</span>
			      <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>			
        </li>
        
    </ul>
    </section>
  </aside>