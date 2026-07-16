<!-- Page content start here-->
        <div class="page-content header-clear-medium">

<div class="card card-style no-shadow">
        <div class="content">
            <div class="text-center">
                <img src="../../assets/images/chat.png?v=1" style="width:90px; height:80px;">
                <h1 class="mb-0">Customer Support</h1>
                <p class="mb-4 font-600 color-highlight">Facing Any Issue? We Are Here For You</p>
            </div>

   
    <button type="submit" name="queryreport" style="width: 100%; border-radius:1rem !important;" 
    class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4"
    data-menu="report-list-box">
    <i class="fa fa-envelope"></i> Submit A New Issue
    </button>
                
    </div>
</div>

  <?php if(!empty($data)) { ?> 
  <?php $i = 1; foreach($data as $list) {  ?>
    
<div class="m-3 p-3 bg-white border rounded-sm">
<em><b class="text-danger">Query:</b> <?php echo $list['query'];?> 
<?php if($list['user_read'] == 0) { ?> <b class="text-danger float-end"> Unread </b> <?php } ?>
</em>        
 <p class="mt-3">               
<?php $max_length = 70; ?>                
 <?php  if (strlen($list['reply']) > $max_length) {?>               
    <?php $shortened_reply = substr($list['reply'], 0, $max_length) . "..."; } else { ?>            
       <?php $shortened_reply = $list['reply']; } ?>
        <?php echo "<strong>{$list['replyby']}:</strong> " . $shortened_reply . " <a href='support?id={$list['id']}'>  View Message</a>"; ?>
           </p> </div>
           <?php $i++; } }  else { echo "<h3 class='text-danger'>No Message To Display</h3>"; } ?>
           </div>
            

<div id="report-list-box" class="menu menu-box-bottom rounded-l" data-menu-effect="menu-over" style="display: block; height: 90vh; background:#ffffff;">
    <div class="menu-title">
        <h1 class="font-24 mb-0 pb-0">Report An Issue</h1>
        <a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
    </div>
    <hr />
    <div class="content mb-0 mt-0">
        <h4 class="mb-3">You have any complain ?</h4>
        <div class="input-style has-borders validate-field mb-4">
            <form method="post">
                <input type="hidden" name="ref" value="0">
                <textarea type="text" name="queryContent" placeholder="Type Message" required></textarea>
                <button type="submit" name="queryreport" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-" id="payvesbtn" onclick="$('#payvesbtn').removeClass('btn-primary'); $('#payvesbtn').addClass('btn-secondary'); $('#payvesbtn').html('<i class=\'fa fa-spinner fa-spin\'></i> Submitting...'); $('#payvesform').submit();">Submit Complain</button>
            </form>
        </div>
    </div>
