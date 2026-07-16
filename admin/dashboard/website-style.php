<div class="row">
<div class="col-12">

<div class="box">
    <div class="box-header with-border d-flex align-items-center justify-content-between">
        <h4 class="box-title">Style Settings</h4>
        <a class="btn btn-info btn-rounded text-white" href="configurations">
              <i class="fa fa-plug" aria-hidden="true"></i> Back
        </a>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
    <form  method="post" class="form-submit">
                
            <div class="form-group">
                <label for="success" class="control-label">User Home Page Color</label>
                <div class="">
                <input type="color" name="sitecolor" value="<?php echo $data->sitecolor; ?>" class="form-control" required="required">
                </div>
            </div>

            <div class="form-group">
                <marquee>
                    <img src="../../assets/images/style/1.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/style/2.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/style/3.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/style/4.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/style/5.png?v=1" width="300" class="border shadow mr-5" />
                </marquee> 
            </div>

            <div class="form-group">
                <label for="success" class="control-label">Login Style & Design</label>
                <div class="">
                <select name="loginstyle" class="form-control" required="required">
                <option value="<?php echo $data->logindesign; ?>" selected>Design <?php echo $data->logindesign; ?></option>
                <option value="1">Design 1</option>
                <option value="2">Design 2</option>
                <option value="3">Design 3</option>
                <option value="4">Design 4</option>
                <option value="5">Design 5</option>
                </select>
                </div>
            </div>

            <div class="form-group">
                <marquee direction="right">
                    <img src="../../assets/images/homepages/h1.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/homepages/h2.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/homepages/h3.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/homepages/h4.png?v=1" width="300" class="border shadow mr-5" /> 
                    <img src="../../assets/images/homepages/h5.png?v=1" width="300" class="border shadow mr-5" />
                    <img src="../../assets/images/homepages/h6.png?v=1" width="300" class="border shadow mr-5" />
                    <img src="../../assets/images/homepages/h7.png?v=1" width="300" class="border shadow mr-5" />
                </marquee> 
            </div>

            <div class="form-group">
                <label for="success" class="control-label">Home Page Style & Design</label>
                <div class="">
                <select name="homestyle" class="form-control" required="required">
                <option value="<?php echo $data->homedesign; ?>" selected>Home <?php echo $data->homedesign; ?></option>
                <option value="1">Home 1</option>
                <option value="2">Home 2</option>
                <option value="3">Home 3</option>
                <option value="4">Home 4</option>
                <option value="5">Home 5</option>
                <option value="6">Home 6</option>
                <option value="7">Home 7</option>
                </select>
                </div>
            </div>
            
            
            <div class="form-group">
                <div class="">
                   <button type="submit" name="update-site-style" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                </div>
            </div>
    </form>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->
</div>
</div>



