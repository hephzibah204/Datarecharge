<div class="card recent-sales overflow-auto">
        <div class="card-body">
            <?php if(!empty($data = $controller->getQueriesAndReplies())): foreach($data as $list): ?>
            
                <h5 class="card-title"><b><?php echo $list['query']; ?>
                <a href="transaction-details?ref=<?php echo $list['ref']; ?>" class="text-primary"><b> <?php echo $list['ref']; ?></b></a>
                <br><br> 
                <?php echo $list['userEmail']; ?></b> <br><br>
                      <?php echo $list['add_date']; ?></h5>
                    <?php foreach($list['replies'] as $reply): ?>
                    <?php if(!empty($reply['reply'])): ?>
                    <div class="card bg-light mb-3">
                        
                        <div class="card-body">
                            <b class="float-end">[ <?php echo $reply['replyby']; ?> ]</b>
                            <p class="font-400 pt-1 pb-1"><?php echo $reply['reply']; ?></p>
                            <p class="font-400 pt-1 pb-1"><span class="opacity-30"> <?php echo $reply['reply_date']; ?> </span></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                   
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
            
             <div class="input-style has-borders validate-field mb-4 p-3">
                        <form method="post">
                            <input type="hidden" name="issueId" value="<?php echo $list['id']; ?>"> 
                            <input type="text" name="replyContent" placeholder="Reply" class="form-control" required>
                            <button type="submit" name="replyQuery" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 btn-primary mt-4 rounded">Reply</button>
                        </form>
                    </div>
            </div></div>
       