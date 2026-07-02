<!DOCTYPE html>
<html class="loading semi-dark-layout" lang="en" data-layout="semi-dark-layout" data-textdirection="ltr">
  <!-- BEGIN: Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="author" content="opitech">
    <title><?= $page_title; ?></title>
    <link rel="apple-touch-icon" href="<?= base_url(); ?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/extensions/toastr.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/bootstrap-extended.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/colors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/components.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/themes/dark-layout.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/themes/bordered-layout.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/themes/semi-dark-layout.min.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/core/menu/menu-types/vertical-menu.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/pages/page-auth.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/extensions/ext-component-toastr.min.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css">
    <!-- END: Custom CSS-->

  </head>
  <!-- END: Head-->

  <!-- BEGIN: Body-->
  <body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
      <div class="content-overlay"></div>
      <div class="header-navbar-shadow"></div>
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
          <div class="auth-wrapper auth-v2">
            <div class="auth-inner row m-0">


              <a class="brand-logo" href="#">
              	<img class="brand-img mr-10" src="<?= base_url(); ?>/app-assets/images/logo/opitech-logo-dark.png" alt="brand" style="width: 140px;">
              </a>
          
              <!-- Left Text-->
              <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src="<?= base_url(); ?>app-assets/images/pages/login-v2.svg" alt="Login V2"/></div>
              </div>
              <!-- /Left Text-->
              <!-- Login-->
              <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto" id="authfarm">
                  <h2 class="card-title fw-bold mb-1"><?= $welcome_message; ?></h2>
                  <p class="card-text mb-2"><?= $login_page_text; ?></p>
                  <form class="auth-login-form mt-2" action="<?= base_url('login/auth'); ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-1">
                      <label class="form-label" for="email">Email</label>
                      <input class="form-control" id="email" type="text" name="email" placeholder="example@mail.com" aria-describedby="email" autofocus="" tabindex="1"/>
                    </div>
                    <div class="mb-1">
                      <div class="input-group input-group-merge form-password-toggle">
                        <input class="form-control form-control-merge" id="password" type="password" name="password" placeholder="············" aria-describedby="password" tabindex="2"/><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                      </div>
                    </div>                   
                    <button id="btnsubmit" class="btn btn-primary w-100" tabindex="4">Sign in</button>
                  </form>
                </div>
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto" id="otpform" style="display: none;">
                  <h2 class="card-title fw-bold mb-1"><?= $welcome_message; ?></h2>
                  <p class="card-text mb-2">Please enter your OTP and start the adventure</p>
                  <form class="otp-login-form mt-2" action="<?= base_url('login/verifyOtp'); ?>" method="POST">
                    <?= csrf_field() ?>
                    <input hidden name="user_id" id="user_id">
                    <div class="mb-1">
                      <label class="form-label" for="otp">OTP</label>
                      <input class="form-control" id="otp" type="text" name="otp" placeholder="OTP" aria-describedby="otp" maxlength="6" />
                    </div>                   
                    <button id="otpsubmit" class="btn btn-primary w-100" tabindex="4">Verify</button>
                  </form>
                </div>
              </div>
              <!-- /Login-->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="<?= base_url(); ?>app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="<?= base_url(); ?>app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/extensions/toastr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?= base_url(); ?>app-assets/js/core/app-menu.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/core/app.min.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- <script src="<?= base_url(); ?>app-assets/js/scripts/pages/page-auth-login.js"></script> -->
    <script src="<?= base_url(); ?>app-assets/js/scripts/extensions/ext-component-toastr.min.js"></script>
    <!-- END: Page JS-->

    <script>
        $(document).ready(function() {
          $('input[type="text"]').attr('autocomplete', 'off').val('');
          $('input[type="password"]').attr('autocomplete', 'off').val('');
          $('input[type="email"]').attr('autocomplete', 'off').val('');

          $('#otp').on('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ($.inArray(e.keyCode, [8, 9, 27, 13]) !== -1 ||
                // Allow: Ctrl/cmd+A
                (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            
            // Ensure that it is a number
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57))) {
                e.preventDefault();
            }
            
            // Get current value and potential new value
            var currentValue = $(this).val();
            var newValue = currentValue + String.fromCharCode(e.which);
            
            // Remove all non-digit characters for length check
            var digitsOnly = newValue.replace(/\D/g, '');
            
            // UK numbers: 10-11 digits (excluding international code)
            // Ireland numbers: 9-10 digits (excluding international code)
            // Allow up to 6 digits to accommodate international numbers
            if (digitsOnly.length > 6) {
                e.preventDefault();
            }
        });
        });  

        var validator=$(".auth-login-form").validate({
          rules :{
            'email':{
                required:false,
                email:false
            },
            'password':{
              required:false,
              minlength:5
            }
          }, messages :{
            'email': "Please enter valid email address",
            "password":{
              required:"Please enter your password",
              minlength:"Password must be 5 char long"
            }
          },submitHandler: function(form) { 
            var formData=$(form).serialize();
            $.ajax({
              url: $(form).attr("action"),
              type: 'POST',
              dataType: 'json',
              data:formData,
              beforeSend: function() {
                 $("#btnsubmit").attr("disabled", true); 
              },
              success: function (data) {        
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: false,
                      progressBar: true
                    });
                    if (data.user_id) {
                      $('#user_id').val(data.user_id);
                      $('#authfarm').hide();
                      $('#otpform').show();
                    }else if(data.role_name =='DRT'){
                      window.location.href="<?= base_url('bookings/driver/view'); ?>";
                    }else{
                      window.location.href="<?= base_url('dashboard'); ?>";
                    }
                  }else{
                    if(data.errors){
                        validator.showErrors(data.errors);
                    }else{              
                      toastr['error'](data.message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                      });
                    }
                  }
              },
              error: function(xhr) {                
                $("#btnsubmit").attr("disabled", false);                        
              },
              complete: function() {
                $("#btnsubmit").attr("disabled", false);
              }
            });       
            return false;
          }
        });

        var verify=$(".otp-login-form").validate({
          rules :{
            'otp':{
                required:false,
                otp:false
            }
          }, messages :{
            'email': "Please enter valid OTP"
          },submitHandler: function(form) { 
            var formData=$(form).serialize();
            $.ajax({
              url: $(form).attr("action"),
              type: 'POST',
              dataType: 'json',
              data:formData,
              beforeSend: function() {
                 $("#otpsubmit").attr("disabled", true);
              },
              success: function (data) {        
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: false,
                      progressBar: true
                    });
                    window.location.href="<?= base_url('dashboard'); ?>";
                  }else{
                    if(data.errors){
                        verify.showErrors(data.errors);
                    }else{              
                      toastr['error'](data.message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                      });
                    }
                  }
              },
              error: function(xhr) {                
                $("#otpsubmit").attr("disabled", false);                        
              },
              complete: function() {
                $("#otpsubmit").attr("disabled", false);
              }
            });       
            return false;
          }
        });
      $(window).on('load',  function(){
        if (feather) {
          feather.replace({ width: 14, height: 14 });
        }
      })
    </script>
  </body>
  <!-- END: Body-->
</html>