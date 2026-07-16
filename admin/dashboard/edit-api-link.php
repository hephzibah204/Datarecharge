<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit API Link</h4>
            </div>
            <div class="card-body">
                <div>
                    <a class="btn btn-primary btn-rounded btn-sm text-white" href="api-setting">
                        <i class="fa fa-download" aria-hidden="true"></i> API Configurations
                    </a>
                    <a class="btn btn-primary btn-sm btn-rounded text-white ml-2" href="api-link">
                        <i class="fa fa-plug" aria-hidden="true"></i> Back
                    </a>
                </div>
                <div class="box-body">
                    <form method="post" class="form-submit">
                        <div class="form-group">
                            <label for="success" class="control-label">API Name:</label>
                            <div>
                                <input type="text" id="type" name="name" value="<?php echo isset($_GET['name']) ? $_GET['name'] : ''; ?>" class="form-control" required="required">
                            </div>
                        </div><br/>

                        <div class="form-group">
                            <label for="success" class="control-label">API Type:</label>
                            <div>
                                <input type="text" id="type" name="type" value="<?php echo isset($_GET['type']) ? $_GET['type'] : ''; ?>" class="form-control" required="required">
                            </div>
                        </div><br/>

                        <div class="form-group">
                            <label for="success" class="control-label">API URL:</label>
                            <div>
                                <input type="text" id="type" name="value" value="<?php echo isset($_GET['value']) ? $_GET['value'] : ''; ?>" class="form-control" required="required">
                                <input type="hidden" name="id" value="<?php echo isset($_GET['aId']) ? $_GET['aId'] : ''; ?>">
                            </div>
                        </div><br/>
                       
                       <div class="form-group">
                            <div>
                               <button type="submit" name="edit-api-link" class="btn btn-primary btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Edit API Details</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
