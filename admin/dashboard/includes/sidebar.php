<?php
  // Determine current active section for menu expansion
  $curUrl = $url ?? '';
  $isNinGroup = in_array($curUrl, ['nin-modifications', 'nin-advanced', 'bulk-validation', 'ipe-clearance', 'nin-fee-settings', 'nin-slip-pricing']);
  $isServicesGroup = in_array($curUrl, ['all-services', 'exam-pin', 'providers', 'airtime-discount', 'recharge-pin-discount', 'data-plans', 'data-pins', 'cable-tv', 'electricity-bill', 'cashback', 'alpha-topup', 'alpha-request', 'airtime-to-cash', 'smile-order', 'smile-plans', 'airtime-pin-stock', 'data-pin-stock']);
  $isUserGroup = in_array($curUrl, ['subscribers', 'credit-user-account', 'credit-user', 'system-users', 'agents']);
  $isFinanceGroup = in_array($curUrl, ['transactions-list', 'transactions', 'processing-transactions', 'manual-orders', 'failed-transactions', 'sales-by-user', 'sale-analysis', 'data-sale-analysis', 'wallet-balance']);
  $isSystemGroup = in_array($curUrl, ['configurations', 'site-setting', 'website-style', 'contact-setting', 'network-setting', 'api-setting', 'monnify-setting', 'paystack-setting', 'payment-gateway', 'blacklist-setting', 'blacklist-number', 'cac', 'referral', 'system-update', 'messaging', 'notifications', 'messages', 'issues']);
?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar">
		
      <!-- sidebar menu-->
      <ul class="sidebar-menu" data-widget="tree">

        <li class="header nav-small-cap">MAIN NAVIGATION</li>

        <!-- Overview -->
        <li class="<?php echo ($curUrl == '' || $curUrl == 'dashboard') ? 'active' : ''; ?>">
          <a href="<?php echo $urlAddon; ?>dashboard">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>			
        </li>

        <li class="<?php echo ($curUrl == 'community') ? 'active' : ''; ?>">
          <a href="<?php echo $urlAddon; ?>community">
            <i class="fa fa-cubes"></i> <span>Community</span>
          </a>			
        </li>

        <!-- Group 1: NIN & Identity Management -->
        <li class="treeview <?php echo $isNinGroup ? 'active menu-open' : ''; ?>">
          <a href="#">
            <i class="fa fa-id-card"></i> <span>NIN & Identity</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo ($curUrl == 'nin-modifications' || $curUrl == 'nin-advanced') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>nin-modifications"><i class="fa fa-circle-o"></i> NIN Advanced Services</a>
            </li>
            <li class="<?php echo ($curUrl == 'bulk-validation') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>bulk-validation"><i class="fa fa-circle-o"></i> Bulk NIN Validation</a>
            </li>
            <li class="<?php echo ($curUrl == 'ipe-clearance') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>ipe-clearance"><i class="fa fa-circle-o"></i> IPE Clearance</a>
            </li>
            <li class="<?php echo ($curUrl == 'nin-fee-settings' || $curUrl == 'nin-slip-pricing') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>nin-fee-settings"><i class="fa fa-circle-o"></i> NIN Fees & Pricing</a>
            </li>
          </ul>
        </li>

        <!-- Group 2: Services & Providers -->
        <li class="treeview <?php echo $isServicesGroup ? 'active menu-open' : ''; ?>">
          <a href="#">
            <i class="fa fa-list"></i> <span>Services & Pins</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo ($curUrl == 'all-services') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>all-services"><i class="fa fa-circle-o"></i> All Services</a>
            </li>
            <li class="<?php echo ($curUrl == 'exam-pin') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>exam-pin"><i class="fa fa-circle-o"></i> Exam Pins</a>
            </li>
            <li class="<?php echo ($curUrl == 'providers') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>providers"><i class="fa fa-circle-o"></i> Providers & Routing</a>
            </li>
          </ul>
        </li>

        <!-- Group 3: User & Customer Management -->
        <li class="treeview <?php echo $isUserGroup ? 'active menu-open' : ''; ?>">
          <a href="#">
            <i class="fa fa-users"></i> <span>User Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo ($curUrl == 'subscribers') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>subscribers"><i class="fa fa-circle-o"></i> Customers / Subscribers</a>
            </li>
            <li class="<?php echo ($curUrl == 'credit-user-account' || $curUrl == 'credit-user') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>credit-user-account"><i class="fa fa-circle-o"></i> Credit / Debit User</a>
            </li>
            <?php if(AdminController::$role == 1): ?>
            <li class="<?php echo ($curUrl == 'system-users') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>system-users"><i class="fa fa-circle-o"></i> System Admins</a>
            </li>
            <?php endif; ?>
          </ul>
        </li>

        <!-- Group 4: Finance & Transactions -->
        <li class="treeview <?php echo $isFinanceGroup ? 'active menu-open' : ''; ?>">
          <a href="#">
            <i class="fa fa-money"></i> <span>Finance & Sales</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo ($curUrl == 'transactions-list' || $curUrl == 'transactions') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>transactions-list"><i class="fa fa-circle-o"></i> Transactions</a>
            </li>
            <?php if(AdminController::$role == 1): ?>
            <li class="<?php echo ($curUrl == 'wallet-balance') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>wallet-balance"><i class="fa fa-circle-o"></i> API Wallet Balances</a>
            </li>
            <?php endif; ?>
          </ul>
        </li>

        <!-- Group 5: System & Settings (Super Admin & Admin) -->
        <li class="treeview <?php echo $isSystemGroup ? 'active menu-open' : ''; ?>">
          <a href="#">
            <i class="fa fa-cogs"></i> <span>System Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(AdminController::$role == 1): ?>
            <li class="<?php echo ($curUrl == 'configurations') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>configurations"><i class="fa fa-circle-o"></i> Configurations</a>
            </li>
            <li class="<?php echo ($curUrl == 'system-update') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>system-update"><i class="fa fa-circle-o"></i> System Update</a>
            </li>
            <?php endif; ?>
            <li class="<?php echo ($curUrl == 'messaging') ? 'active' : ''; ?>">
              <a href="<?php echo $urlAddon; ?>messaging"><i class="fa fa-circle-o"></i> Messaging / Notifications</a>
            </li>
          </ul>
        </li>

        <li class="header nav-small-cap">ACCOUNT</li>

        <li class="<?php echo ($curUrl == 'manage-account') ? 'active' : ''; ?>">
          <a href="<?php echo $urlAddon; ?>manage-account">
            <i class="fa fa-user"></i> <span>Manage Account</span>
          </a>			
        </li>

        <li>
          <a href="<?php echo $urlAddon; ?>logout">
            <i class="fa fa-lock"></i> <span>Logout</span>
          </a>			
        </li>
        
      </ul>
    </section>
</aside>