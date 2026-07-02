<?php 
    $AUTH=session()->get('AUTH');
    $uri = new \CodeIgniter\HTTP\URI(); 
    $uri = current_url(true);

    $node1="dashboard";
    $node2="";
    $node3="";
    if($uri->getSegment(1))
    {
        $node1=$uri->getSegment(1);
    }
    if($uri->getSegment(2))
    {
        $node2=$uri->getSegment(2);
    } 
 

    if($node2!="")
    {
        $allowed=base_url($node1.'/'.$node2);
    }else{
        $allowed=base_url($node1);
    }   

    $redirect_url=base_url('bookings/view');
    $allowed_url=[base_url('bookings/view'),base_url('bookings/details'),base_url('profile')];

    if ($AUTH['role_id'] == 11 && $AUTH['role_name'] == 'Capacity') 
    {
        $redirect_url=base_url('reports/bookings/capacity');
        $allowed_url=[base_url('reports/bookings/capacity'), base_url('profile')];
        

        $allowed=base_url($node1.'/'.$node2.'/capacity');
        if (!in_array($allowed, $allowed_url)) 
        {
            header("Location: $redirect_url");
            exit($redirect_url);
        }
    }  

    if($AUTH['role_id']=="3" || $AUTH['role_name'] == 'Driver' || $AUTH['role_name'] == 'SourceBase')
    {  
        if (!in_array($allowed, $allowed_url)) 
        {
            header("Location: $redirect_url");
            exit($redirect_url);
        }
    }
?>
<!DOCTYPE html>
<html class="loading semi-dark-layout" lang="en" data-layout="semi-dark-layout" data-textdirection="ltr">
  <!-- BEGIN: Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title><?= $this->renderSection("title"); ?></title>
    <link rel="apple-touch-icon" href="<?= base_url(); ?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/pickers/pickadate/pickadate.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/extensions/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/forms/wizard/bs-stepper.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/forms/form-wizard.min.css">

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
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/pages/dashboard-ecommerce.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/pages/ui-feather.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/charts/chart-apex.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/extensions/ext-component-toastr.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/extensions/ext-component-sweet-alerts.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/plugins/forms/pickers/form-pickadate.min.css">
    <!-- ck-editor -->
    <!-- <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js" integrity="sha512-OF6VwfoBrM/wE3gt0I/lTh1ElROdq3etwAquhEm2YI45Um4ird+0ZFX1IwuBDBRufdXBuYoBb0mqXrmUA2VnOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css">
    <!-- END: Custom CSS-->
    <style type="text/css">
       /* body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;  
            user-select: none;      
        }*/
        .app-content{
            -webkit-user-select: text; /* Safari */
            -moz-user-select: text; /* Firefox */
            -ms-user-select: text; /* Internet Explorer/Edge */
            user-select: text; /* Standard */
        }
        div.dt-button-collection {
          width: 215px;
        }

        .display.dataTable {
          font-family: Verdana, Geneva, Tahoma, sans-serif;
          font-size: 12px;
        }

        td.none {
          display: none;
        }

        div.dt-buttons {
            padding: 15px 10px 0;
            float: left!important;
        }

        .dataTables_length {
            float: left !important;
        }
        .btn-group{
            margin: 0 10px;
        }
        .status-sent{
            background: #38E8B8;
        }
        .status-received{
            background: #26A6F8;
        }
        .status-failed{
            background: #CF1868;
        }
        .low-price {
            color: red; /* Optional: for visibility */
            font-weight: bold;
        }
        .high-price{
            color: darkgreen;
            font-weight: bold;
        }
        .row_dubai{
            background-color: #7367F0;
            font-weight: bold;
            color: #fff;
        }
        .more-80{
            color: orange;
        }
        .more-90{
            color: darkred;
        }
        /*Repeated Booking css*/
        /*.row_repeated{
            color: #7367F0;
            font-weight: bold;
        }*/

    </style>
  </head>
   <body class="pace-done vertical-layout vertical-menu-modern menu-expanded footer-fixed navbar-sticky" data-open="click" data-menu="vertical-menu-modern" data-col="">
   
    <!-- BEGIN: NAV-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center navbar-shadow navbar-light fixed-top">
      <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
          <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
          </ul>       
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">          
          <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style" onclick="change_theme();"><i class="ficon" data-feather="moon"></i></a></li>
          <!-- <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon" data-feather="search"></i></a> -->
            <div class="search-input">
              <div class="search-input-icon"><i data-feather="search"></i></div>
              <input class="form-control input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="search">
              <div class="search-input-close"><i data-feather="x"></i></div>
              <ul class="search-list search-list-main"></ul>
            </div>
          </li>
          <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder"><?= $AUTH['first_name']; ?>  <?= $AUTH['last_name']; ?></span><span class="user-status"><?= $AUTH['role_name'].'<br>'.date('Y-m-d h:i:s'); ?></span></div><span class="avatar"><div class="avatar bg-danger">
              <div class="avatar-content"><?= substr($AUTH['first_name'], 0, 1); ?><?= substr($AUTH['last_name'], 0, 1); ?></div>
            </div></a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
              <a class="dropdown-item" href="<?= base_url('profile'); ?>"><i class="me-50" data-feather="user"></i> Profile</a>
              <div class="dropdown-divider" style="display: none;"></div>
              <a style="display: none;" class="dropdown-item" href="#"><i class="me-50" data-feather="settings"></i> Settings</a>
              <a class="dropdown-item" href="<?= base_url('users/logout'); ?>"><i class="me-50" data-feather="power"></i> Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
	<!-- End: NAV-->
 
 
 
   <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto"><a class="navbar-brand" href="<?= base_url("dashboard"); ?>">
                            <span class="brand-logo d-none">
                                <img src="<?= base_url(); ?>app-assets\images\logo\logo.png" />
                            </span>
                        <h2 class="brand-text">Globel Parking</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <?php get_menu($node1,$node2); ?>

                <!-- <li class="active nav-item">
                    <a class="d-flex align-items-center" href="<?= base_url('dashboard'); ?>">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate" data-i18n="Home">Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="<?= base_url('users'); ?>">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate" data-i18n="users">Users</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="#">
                    <i data-feather='bookmark'></i>
                        <span class="menu-title text-truncate" data-i18n="server">Bookings</span>
                    </a>
                </li>  

                <li class="nav-item">
                    <a class="d-flex align-items-center" href="#">
                    <i data-feather='dollar-sign'></i>
                        <span class="menu-title text-truncate" data-i18n="server">Promotions</span>
                    </a>
                </li>               

                <li class="nav-item">
                    <a class="d-flex align-items-center" href="<?= base_url('operators'); ?>">
                        <i data-feather='user'></i>
                        <span class="menu-title text-truncate" data-i18n="user-check">Operators</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather='file-text'></i>
                        <span class="menu-title text-truncate" data-i18n="user-check">Invoices</span>
                    </a>
                </li>

                <li class="nav-item has-sub">
                    <a class="d-flex align-items-center" href="javascript:void(0);">
                        <i data-feather='database'></i>
                        <span class="menu-title text-truncate" data-i18n="phone-incoming">Reports</span>
                    </a>
                    <ul class="menu-content">
                      <li>
                        <a class="d-flex align-items-center" href="#">
                          <i data-feather="circle"></i>
                          <span class="menu-item text-truncate" data-i18n="circle">All Bookings</span>
                        </a>
                      </li>
                      <li>
                        <a class="d-flex align-items-center" href="#">
                          <i data-feather="circle"></i>
                          <span class="menu-item text-truncate" data-i18n="circle">Cancelled Bookings</span>
                        </a>
                      </li>
                      <li>
                        <a class="d-flex align-items-center" href="#">
                          <i data-feather="circle"></i>
                          <span class="menu-item text-truncate" data-i18n="circle">Summary</span>
                        </a>
                      </li>
                      <li>
                        <a class="d-flex align-items-center" href="#">
                          <i data-feather="circle"></i>
                          <span class="menu-item text-truncate" data-i18n="circle">Performance</span>
                        </a>
                      </li>
                      <li>
                        <a class="d-flex align-items-center" href="#">
                          <i data-feather="circle"></i>
                          <span class="menu-item text-truncate" data-i18n="circle">Operator Bookings</span>
                        </a>
                      </li>                       
                    </ul>
                </li>                    

                    <li class="nav-item">
                        <a class="d-flex align-items-center" href="#">
                            <i data-feather='globe'></i>
                            <span class="menu-title text-truncate" data-i18n="user-check">Websites</span>
                        </a>
                    </li> -->

                

                

            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

	<?= $this->renderSection("content"); ?>


	<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="<?= base_url(); ?>app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->
    <script src="<?= base_url(); ?>app-assets/vendors/js/forms/wizard/bs-stepper.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/scripts/extensions/ext-component-toastr.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/buttons.bootstrap5.min.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/jszip.min.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script> 
    <script src="<?= base_url(); ?>app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script> 

    <script src="<?= base_url(); ?>app-assets/vendors/js/file-uploaders/dropzone.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/pickers/pickadate/picker.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/pickers/pickadate/picker.date.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/pickers/pickadate/picker.time.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/pickers/pickadate/legacy.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/forms/select/select2.full.min.js"></script>    
    <script src="<?= base_url(); ?>app-assets/vendors/js/charts/chart.min.js"></script>


    <!-- BEGIN: Theme JS-->
    <script src="<?= base_url(); ?>app-assets/js/core/app-menu.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/core/app.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/scripts/customizer.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/scripts/components/components-dropdowns.min.js"></script>    
    <script src="<?= base_url(); ?>app-assets/js/scripts/extensions/ext-component-sweet-alerts.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/scripts/ui/ui-feather.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/scripts/forms/form-wizard.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/print-this@1.15.0/printThis.min.js"></script>
    
    <!-- END: Theme JS-->

    
        


    <!-- BEGIN: Page JS-->
    <!-- END: Page JS-->

    <script>
        vdstatus=true;
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        });

        $(document).ready(function() {
          //$('input[type="text"]').attr('autocomplete', 'off').val('');
          //$('input[type="password"]').attr('autocomplete', 'off').val('');
          //$('input[type="email"]').attr('autocomplete', 'off').val('');
        });  
        
        function change_theme()
            {
                console.log("theme-changed");
            }

          function showModal(id)
          {
            $('#'+id).modal('show');
          }

          function hideModal(id)
          {
            $('#'+id).modal('hide');
          }

    </script>
    <?= $this->renderSection("javascript"); ?>
</body>
<!-- END: Body-->
</html>