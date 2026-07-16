<div class="row">
<div class="col-12">
<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>Note: </strong> Please Confrim The Api Links From The Api Provider In Order To Add It To The List
        </div>
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Add New Api</h4>
            <a class="btn btn-info btn-rounded text-white" href="configurations">
                <i class="fa fa-plug" aria-hidden="true"></i> Back
            </a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        
        <form  method="post" class="form-submit">
                    
               
               <input type="hidden" name="code" value="<?php echo date("Hymd") . date("d"); ?>" required />
                    
                
                <div class="form-group">
                    <label for="success" class="control-label">Api Provider Name</label>
                    <div class="">
                    <input type="text" name="providername" placeholder="Api Provider Name" class="form-control" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Api Url</label>
                    <div class="">
                    <input type="text" name="providerurl" placeholder="Api Url" class="form-control" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Service</label>
                    <div class="">
                        <select name="service" class="form-control" required>
                            <option value="">Select Service</option>
                            <option value="Wallet">Wallet</option>
                            <option value="Airtime">Airtime</option>
                            <option value="Data">Data</option>
                            <option value="Recharge Card">Recharge Card</option>
                            <option value="Data Pin">Data Pin</option>
                            <option value="CableVer">Cable Verification</option>
                            <option value="Cable">Cable</option>
                            <option value="ElectricityVer">Electricity Verification</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Exam">Exam</option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <div class="">
                       <button type="submit" name="add-new-api-details" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>