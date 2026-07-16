<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Kuda API</h4>

            <div>
                        <a class="btn btn-info btn-rounded btn-sm text-white" href="kuda-withdrawal">
                            <i class="fa fa-download" aria-hidden="true"></i> Withdraw
                        </a>
                        <a class="btn btn-info btn-sm btn-rounded text-white ml-2" href="configurations">
                            <i class="fa fa-plug" aria-hidden="true"></i> Back
                        </a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

        <div class="alert alert-danger">Note: Use This Withdrawal Option Only When A Customer Virtual Account Funds Withdrawal Were Not Transfered To Your Main Kuda Account</div>
        <form  method="post" class="form-submit">
                    
                <div class="form-group">
                    <label for="success" class="control-label">User Email</label>
                    <div class="">
                    <input type="email" name="userEmail" placeholder="Email" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Amount To Withdraw</label>
                    <div class="">
                    <input type="number" name="userAmount" placeholder="Amount" class="form-control" required="required">
                    </div>
                </div>
                
               
                    

                <div class="form-group">
                    <div class="">
                       <button type="submit" name="withdraw-kuda-funds" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Withdraw</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>



