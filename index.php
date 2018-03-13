<?
include('basic-header.php');
?>

<!-- Form area -->
<div class="admin-form"><center><h2 style='margin-left: auto; margin-right:auto'>TrackIssu.es</h2></center>
  <div class="container">

    <div class="row">
      <div class="col-md-12">
        <!-- Widget starts -->

		<?
			if ($error){
				echo "
				<div class='alert alert-danger'>
				  $lang[Str_Login_Error]
				</div>
				";
			}
		?>
            <div class="widget worange">
              <!-- Widget head -->
              <div class="widget-head">
                <i class="fa fa-lock"></i> <?=$lang['Str_Login'];?> 
              </div>

              <div class="widget-content">
                <div class="padd">
                  <!-- Login form -->
                  <form class="form-horizontal" method='post' action='processors/login.php'>
                    <!-- Email -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="inputUser_Name"><?=$lang['Str_Username'];?></label>
                      <div class="col-lg-9">
                        <input type="text" class="form-control" id="inputUser_Name" placeholder="<?=$lang['Str_Username'];?>" name="User_Name">
                      </div>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="inputPassword"><?=$lang['Str_Password'];?></label>
                      <div class="col-lg-9">
                        <input type="password" class="form-control" id="inputPassword" placeholder="<?=$lang['Str_Password'];?>" name="User_Pass">
                      </div>
                    </div>
					<!-- Remember me checkbox and sign in button -->
                    <div class="form-group">
					<div class="col-lg-9 col-lg-offset-3">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> <?=$lang['Str_Remember_Me'];?>
                        </label>
						</div>
					</div>
					</div>
                        <div class="col-lg-9 col-lg-offset-2">
							<button type="submit" class="btn btn-danger"><?=$lang['Str_Sign_In'];?></button>
							<button type="reset" class="btn btn-default"><?=$lang['Str_Reset'];?></button>
							New to TrackIssu.es? <a href="signup.php">Signup</a>
						</div>
                    <br />
                  </form>
				  
				</div>
                </div>
              
                <!-- <div class="widget-foot">
                  Not Registred? <a href="#">Register here</a>
                </div> -->
            </div>  
      </div>
    </div>
  </div> 
</div>
<?
include('basic-footer.php');
?>