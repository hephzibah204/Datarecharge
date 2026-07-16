     <?php
 
include"inc/config.php";

$sql="SELECT Full_Name FROM users where Phone_Number='$_SESSION[phone_number]'";
$result=mysqli_query($conn, $sql);
$row=mysqli_fetch_assoc($result);
$Full_Name=$row['Full_Name'];


?>





    <!-- Sidebar main menu -->
    <div class="sidebar-wrap  sidebar-pushcontent">
        <!-- Add overlay or fullmenu instead overlay -->
        <div class="closemenu text-muted">Close Menu</div>
        <div class="sidebar dark-bg">
            <!-- user information -->
            <div class="row my-3">
                <div class="col-12 ">
                    <div class="card shadow-sm bg-opac text-white border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto">
                                    <figure class="avatar avatar-44 rounded-15">
                                        <img src="assets/img/user1.jpg" alt="">
                                    </figure>
                                </div>
                                <div class="col px-0 align-self-center">
                                    <p class="mb-1"><?php echo $Full_Name;?></p>
                                    <p class="text-muted size-12"><?php echo $_SESSION['phone_number'];?></p>
                                </div>
                                <div class="col-auto">
                                   
                                </div>
                            </div>
                        </div>
                        <div class="card bg-opac text-white border-0">
                           
                        </div>
                    </div>
                </div>
            </div>

            <!-- user emnu navigation -->
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="dashboard.php">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-house-door"></i></div>
                                <div class="col">Dashboard</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="add_money" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-chat-text"></i></div>
                                <div class="col">Fund Wallet</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                       
                        <li class="nav-item">
                            <a class="nav-link" href="buy_data" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-chat-text"></i></div>
                                <div class="col">Buy Data</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="airtime" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-bell"></i></div>
                                <div class="col">Top Up</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="electricity" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-newspaper"></i></div>
                                <div class="col">Electricity Bill</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="cable" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-palette"></i></div>
                                <div class="col">Cable Subscription <i class="bi bi-star-fill text-warning small"></i></div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                         <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                                aria-expanded="false">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-person"></i></div>
                                <div class="col">Transactions</div>
                                <div class="arrow"><i class="bi bi-plus plus"></i> <i class="bi bi-dash minus"></i>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item nav-link" href="myorders">
                                        <div class="avatar avatar-40 rounded icon"><i class="bi bi-calendar2"></i></div>
                                        <div class="col">My Orders</div>
                                        <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                    </a></li>
                                    <li><a class="dropdown-item nav-link" href="wallet_summary">
                                        <div class="avatar avatar-40 rounded icon"><i class="bi bi-calendar2"></i></div>
                                        <div class="col">Wallet Summary</div>
                                        <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                    </a></li>
                                <li><a class="dropdown-item nav-link" href="payment_history">
                                        <div class="avatar avatar-40 rounded icon"><i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="col">Payments</div>
                                        <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                    </a></li>
                            </ul>
                        </li>

                         <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                                aria-expanded="false">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-person"></i></div>
                                <div class="col">Setting</div>
                                <div class="arrow"><i class="bi bi-plus plus"></i> <i class="bi bi-dash minus"></i>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item nav-link" href="update_password">
                                        <div class="avatar avatar-40 rounded icon"><i class="bi bi-calendar2"></i></div>
                                        <div class="col">Change Password</div>
                                        <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                    </a></li>
                                
                            </ul>
                        </li> 
                          <li class="nav-item">
                            <a class="nav-link" href="developer" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-newspaper"></i></div>
                                <div class="col">Developer API</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>

                        
                        <li class="nav-item">
                            <a class="nav-link" href="signout" tabindex="-1">
                                <div class="avatar avatar-40 rounded icon"><i class="bi bi-box-arrow-right"></i></div>
                                <div class="col">Signout</div>
                                <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar main menu ends -->