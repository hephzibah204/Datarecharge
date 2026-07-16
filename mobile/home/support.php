
<div class="page-content header-clear-medium bg-light">
    <div class="container mt-3">
        <div class="timeline-body mt-0">
            <?php if(!empty($data)): foreach($data as $list): ?>
            
            <div class="chat-bubble chat-bubble-right float-end">
            <?php echo $list['query']; ?>
            <br><span class="opacity-50"><?php echo $list['add_date']; ?></span>
            </div><br><br><br>
             
               <?php foreach($list['replies'] as $reply): ?>
                    <?php if(!empty($reply['reply'])): ?>
                        <div class="timeline-item mt-2">
                       
                       <?php if ($reply['replyby'] === "Admin"): ?>
                                <div class="chat-bubble chat-bubble-left">
                            <?php else: ?>
                                <div class="chat-bubble chat-bubble-right">
                            <?php endif; ?>
                                <?php echo $reply['reply']; ?>
                                <br>
                                <span class="opacity-50"><?php echo $reply['reply_date']; ?></span>
                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="input-style has-borders validate-field mb-4">
                    <form method="post">
                        <input type="hidden" name="issueId" value="<?php echo $list['id']; ?>"> 
                        <input type="text" name="replyContent" placeholder="Reply" required />
                        <button type="submit" name="replyQuery" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-"> Reply </button>
                    </form>
                </div>
            <?php endforeach; else : ?>
                <div class="timeline-item mt-5">
                    <i class="fa fa-envelope bg-blue-dark color-white shadow-l timeline-icon"></i>
                    <div class="timeline-item-content rounded-s">
                        <h5 class="font-400 pt-1 pb-1 text-danger">
                            <b>No Message Available</b>
                        </h5>
                    </div>
                </div>
            <?php endif; ?>	
        </div>
    </div>
</div> 
