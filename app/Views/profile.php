<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
	<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
 <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">                           
                            <?php if(sizeof($breadcrumb)>0) { ?>
                            <h2 class="content-header-title float-start mb-0"><?= $page_title; ?></h2>    
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <?php for($i=0;$i<sizeof($breadcrumb);$i++) { 
                                        extract($breadcrumb[$i]);
                                    ?>
                                    <?php if($link) { ?>
                                        <li class="breadcrumb-item <?= $status; ?>"><a href="<?= $href; ?>"><?= $title; ?></a></li>
                                    <?php }else{ ?>
                                        <li class="breadcrumb-item <?= $status; ?>"><?= $title; ?></li>
                                    <?php } ?>                                    
                                    <?php } ?>
                                </ol>
                            </div>
                            <?php } else { ?>
                                <h2 class=""><?= $page_title; ?></h2>
                            <?php } ?>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">

            	<section id="page-account-settings">
  <div class="row">
    <!-- left menu section -->
    <div class="col-md-3 mb-2 mb-md-0">
      <ul class="nav nav-pills flex-column nav-left">
        <!-- general -->
        <li class="nav-item">
          <a
            class="nav-link active"
            id="account-pill-general"
            data-bs-toggle="pill"
            href="#account-vertical-general"
            aria-expanded="true"
          >
            <i data-feather="user" class="font-medium-3 me-1"></i>
            <span class="fw-bold">General</span>
          </a>
        </li>
        <!-- change password -->
        <li class="nav-item">
          <a
            class="nav-link"
            id="account-pill-password"
            data-bs-toggle="pill"
            href="#account-vertical-password"
            aria-expanded="false">
            <i data-feather="lock" class="font-medium-3 me-1"></i>
            <span class="fw-bold">Change Password</span>
          </a>
        </li>
      </ul>
    </div>
    <!--/ left menu section -->

    <!-- right content section -->
    <div class="col-md-9">
      <div class="card">
        <div class="card-body">
          <div class="tab-content">
            <!-- general tab -->
            <div
              role="tabpanel"
              class="tab-pane active"
              id="account-vertical-general"
              aria-labelledby="account-pill-general"
              aria-expanded="true"
            >
              <!-- header section -->
              <?php
              	 $pic=$user['pic'];
              	 if(is_null($user['pic']) || trim($user['pic'])=="")
              	 {
              	 	$pic=base_url()."app-assets/images/portrait/small/avatar-s-11.jpg";
              	 }
              ?>
              <div class="d-flex">
                <a href="#" class="me-25">
                  <img
                    src="<?= $pic; ?>"
                    id="account-upload-img"
                    class="rounded me-50"
                    alt="profile image"
                    height="80"
                    width="80"
                  />
                </a>
                <!-- upload and reset button -->
                <div class="mt-75 ms-1">
                  <label for="account-upload" class="btn btn-sm btn-primary mb-75 me-75">Upload</label>
                  <input type="file" id="account-upload" hidden accept="image/*" />
                  <button class="btn btn-sm btn-outline-secondary mb-75" id="account-upload-reset">Reset</button>
                  <p>Allowed JPG, GIF or PNG. Max size of 800kB</p>
                </div>
                <!--/ upload and reset button -->
              </div>
              <!--/ header section -->

              <!-- form -->
              <form class="validate-form mt-2 form-general" id="form-general" action="<?= base_url('profile/save'); ?>">
              	<?= csrf_field() ?>
              	<input type="hidden" name="id" value="<?= id_en($user['id']); ?>">
              	<input type="hidden" name="pic" id="pic" value="<?= $pic; ?>">
                <div class="row">
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="first_name">First Name</label>
                      <input
                        type="text"
                        class="form-control"
                        id="first_name"
                        name="first_name"
                        placeholder="First Name"
                        value="<?= $user['first_name']; ?>"
                      />
                    </div>
                  </div>
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="last_name">Last Name<?= $user['last_name']; ?></label>
                      <input
                        type="text"
                        class="form-control"
                        id="last_name"
                        name="last_name"
                        placeholder="Last Name"
                        value="<?= $user['last_name']; ?>"
                      />
                    </div>
                  </div>
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="email">Email</label>
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Email"
                        value="<?= $user['email']; ?>"
                      />
                    </div>
                  </div>
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="phone">Phone</label>
                      <input
                        type="text"
                        class="form-control"
                        id="phone"
                        name="phone"
                        placeholder="Phone"
                        value="<?= $user['phone']; ?>"
                      />
                    </div>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary mt-2 me-1" id="btnsubmit-general">Save changes</button>                    
                  </div>
                </div>
              </form>
              <!--/ form -->
            </div>
            <!--/ general tab -->

            <!-- change password -->
            <div
              class="tab-pane fade"
              id="account-vertical-password"
              role="tabpanel"
              aria-labelledby="account-pill-password"
              aria-expanded="false"
            >
              <!-- form -->
              <form class="validate-form form-change-password" id="form-change-password" action="<?= base_url('profile/change_password'); ?>">
              	<?= csrf_field() ?>
              	<input type="hidden" name="id" value="<?= id_en($user['id']); ?>">
                <div class="row">
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="old_password">Old Password</label>
                      <div class="input-group form-password-toggle input-group-merge">
                        <input
                          type="password"
                          class="form-control"
                          id="old_password"
                          name="old_password"
                          placeholder="Old Password"
                        />
                        <div class="input-group-text cursor-pointer">
                          <i data-feather="eye"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="new_password">New Password</label>
                      <div class="input-group form-password-toggle input-group-merge">
                        <input
                          type="password"
                          id="new_password"
                          name="new_password"
                          class="form-control"
                          placeholder="New Password"
                        />
                        <div class="input-group-text cursor-pointer">
                          <i data-feather="eye"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-sm-6">
                    <div class="mb-1">
                      <label class="form-label" for="confirm_password">Retype New Password</label>
                      <div class="input-group form-password-toggle input-group-merge">
                        <input
                          type="password"
                          class="form-control"
                          id="confirm_password"
                          name="confirm_password"
                          placeholder="Confirm Password"
                        />
                        <div class="input-group-text cursor-pointer"><i data-feather="eye"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary me-1 mt-1">Save changes</button>
                    <button type="reset" class="btn btn-outline-secondary mt-1">Cancel</button>
                  </div>
                </div>
              </form>
              <!--/ form -->
            </div>
            <!--/ change password -->
          </div>
        </div>
      </div>
    </div>
    <!--/ right content section -->
  </div>
</section>

            
            </div>
        </div>
    </div>
    <!-- END: Content-->
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script>
	var defAvatar="<?= base_url(); ?>app-assets/images/portrait/small/avatar-s-11.jpg";
	accountUploadImg = $('#account-upload-img');
    accountUploadBtn = $('#account-upload');
    accountUploadRest = $("#account-upload-reset");
    accountUploadBtn.on('change', function (e) {
      var reader = new FileReader(),
        files = e.target.files;
      reader.onload = function () {
        if (accountUploadImg) 
        {
          accountUploadImg.attr('src', reader.result);
          $("#pic").val(reader.result);
        }
      };
      reader.readAsDataURL(files[0]);
    });

    var validator=$(".form-general").validate({
          rules :{
            'first_name':{
                required:vdstatus,
                minlength:2
            },
            'email':{
                required:vdstatus,
                email:vdstatus
            }
          }, messages :{
            "first_name":{
              required:"Please enter your first name",
              minlength:"first name must be 2 char long"
            },
            'email': "Please enter valid email address"
          },submitHandler: function(form) { 
        var formData=$(form).serialize();
              $.ajax({
              url: $(form).attr("action"),
              type: 'POST',
              dataType: 'json',
              data:formData,
              beforeSend: function() {
                 $("#btnsubmit-general").attr("disabled", true);
              },
              success: function (data) {        
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                  }else{
                    if(data.errors){
                        validator.showErrors(data.errors);
                    }else{              
                      toastr['error'](data.message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true,
                      });
                    }
                  }
              },
              error: function(xhr) {                
                $("#btnsubmit-general").attr("disabled", false);                        
              },
              complete: function() {
                $("#btnsubmit-general").attr("disabled", false);                
              }
          });       
        return false;
      }
      });


    var validator=$(".form-change-password").validate({
          rules :{
            'old_password':{
              required:vdstatus,
              minlength:5
            },
            'new_password':{
              required:vdstatus,
              minlength:5
            },
            'confirm_password':{
              required:vdstatus,
              minlength:5
            }
          }, messages :{
            "old_password":{
              required:"Please enter your old password",
              minlength:"Password must be 5 char long"
            },
            "new_password":{
              required:"Please enter your new password",
              minlength:"Password must be 5 char long"
            },
            "confirm_password":{
              required:"Please enter your confirm password",
              minlength:"Password must be 5 char long",
              equalTo: "#new_password"
            }
          },submitHandler: function(form) { 
        var formData=$(form).serialize();
              $.ajax({
              url: $(form).attr("action"),
              type: 'POST',
              dataType: 'json',
              data:formData,
              beforeSend: function() {
                 $("#btnsubmit-general").attr("disabled", true);
              },
              success: function (data) {        
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                  }else{
                    if(data.errors){
                        validator.showErrors(data.errors);
                    }else{              
                      toastr['error'](data.message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true,
                      });
                    }
                  }
              },
              error: function(xhr) {                
                $("#btnsubmit-general").attr("disabled", false);                        
              },
              complete: function() {
                $("#btnsubmit-general").attr("disabled", false);                
              }
          });       
        return false;
      }
      });

    $("#account-upload-reset").on('click',function(e){
    	$('#account-upload').val("");
    	$("#pic").val("");
    	$('#account-upload-img').attr('src',defAvatar);
    });

</script>
<?= $this->endSection(); ?>