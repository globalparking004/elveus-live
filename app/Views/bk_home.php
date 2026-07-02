<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
	<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
 <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
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
                                <!-- <h2 class=""><?= $page_title; ?></h2> -->
                            <?php } ?>    
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none" style="display:none!important;">
                    <div class="mb-1 breadcrumb-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="grid"></i></button>
                            <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="app-todo.html"><i class="me-1" data-feather="check-square"></i><span class="align-middle">Todo</span></a><a class="dropdown-item" href="app-chat.html"><i class="me-1" data-feather="message-square"></i><span class="align-middle">Chat</span></a><a class="dropdown-item" href="app-email.html"><i class="me-1" data-feather="mail"></i><span class="align-middle">Email</span></a><a class="dropdown-item" href="app-calendar.html"><i class="me-1" data-feather="calendar"></i><span class="align-middle">Calendar</span></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">


                <div class="row">

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                               
                     
                                
                                
    <style>
  .page-header h2 {
    display:none !important;
  }

  </style>


                                <div class="container text-center" style="margin-bottom:200px">
                  <h1>Today's Performance</h1>
                  <div class="col-sm-12 text-center">
                    <div class="row">
                      <h1 class="green" style="margin-bottom:0px !important; font-size:220px;"><?= $stats['completed_bookings']; ?></h1>
                      <h1 style="margin-top: -20px;">SALES</h1>
                      </div>
                  </div>

                  <div class="col-sm-12 text-center">
                    <div class="row">
                      <h1 style="margin-top:100px; font-size:40px;">£<?= $stats['profit']; ?> Gross Profit</h1>
                      </div>
                      <div class="row">
                        <h1 style="margin-top:10px; font-size:40px;">£<?= $stats['avg']; ?> Avg Per Booking</h1>
                      </div>
                  </div>
                </div>
                  



                                </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- END: Content-->
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>

<?= $this->endSection(); ?>

