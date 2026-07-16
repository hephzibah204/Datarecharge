<section class="h-100" style="margin-top:-700px;">
		<div class="container h-100">
			<div class="row justify-content-sm-center h-100">
					<div class="card shadow-lg">
						<div class="card-body p-5">
							<h1 class="fs-4 card-title fw-bold mb-4">Profile </h1>
	
   
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-xs-12 profile-badge">
                <!-- <img src="https://dummyimage.com/600x400/000/"> -->
                
                <!--div class="profile-pic">
                 
                        <img class="profile-pics" alt="User Pic" src="../../assets/img/foreverdata.jpg" id="profile-image1" height="200">
                        <input id="profile-image-upload" class="hidden" type="file" onchange="previewFile()" >
                        <div style="color:#999;" >  </div-->
                        
                </div>
                <div class="user-detail text-center">
                 <hr>
                  <div class="form-group">  
                    <label for="Fname">.</label>
                    
                  </div>

                  <div class="form-group">  
                    
                    <h4><?php echo $data->sFname. " " . $data->sLname; ?></h4>

                  </div>
                
                  <div class="form-group" class="text-centre">  
                    <h5><?php echo $data->sPhone; ?></h5>
  <h6><?php echo $data->sEmail; ?></h6>
  <h6>Nigeria, <?php echo $data->sState; ?></h6>
  
  <hr>
                  <h5>Security</h5>
 <a href="pin">
     <input type="submit" class="btn btn-info" value="Update password">
 </a>
 <a href="psswd">
     <input type="submit" class="btn btn-info" value="Update Pin">
 </a> 
                        <hr>
                        <h5>Account Type: <?php echo $controller->formatUserType($data->sType); ?></h5>
                        <a href="#agent-upgrade-modal" id="upgrade-agent-btn" data-menu="agent-upgrade-modal">
     <input type="submit" class="btn btn-info" value="Update to Super">
 </a>
 <a href="#vendor-upgrade-modal" id="vendor-agent-btn" data-menu="vendor-upgrade-modal">
     <input type="submit" class="btn btn-info" value="Update to Reseller">
 </a>
                     <hr>
                     <h5>Developer</h5>
                     <a href="apidocumentation">
     <input type="submit" class="btn btn-info" value="Api Documentation">
 </a>
 <a href="pricing">
     <input type="submit" class="btn btn-info" value="price list">
 </a> 
 <hr>
 <a href="#">
                                <input type="text" class="form-control" readonly value="<?php echo $data->sApiKey; ?>" />
                            </a>
 <a href="#">
                                <button class="btn btn-danger btn-sm" onclick="copyToClipboard('<?php echo $data->sApiKey; ?>')">Copy Api Key</button>
                                <?php if(!empty($data2)): ?>
                                    
                                <?php endif; ?>
                            </a>
                        
                </div>
 
 </div></div></div></div>
 </div>
	


