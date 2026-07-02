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
                <div class="content-header-left col-md-8 col-12 mb-2">
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
                <div class="content-header-right text-md-end col-md-4 col-12 d-md-block d-none"></div>
            </div>
            <div class="content-body">   

                <section id="ajax-datatable">
                    <div class="row">
                        <div class="col-12">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php elseif(session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>
                          <!-- <a href="<?php echo base_url('database_export?backup=1')?>" class="btn btn-primary"><i class="fa fa-database"></i> DB Export</a> -->
                          <!-- <button type="button" id="dbBakcup" class="btn btn-primary"><i class="fa fa-database"></i> DB Export</button> -->
                          <a href="<?php echo base_url('booking_export')?>" class="btn btn-primary"><i class="fa fa-database"></i> Bookings Export</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<?= $this->endSection(); ?>
<script>
    $(function() {
        $('#dbBakcup').click(function() {
            $.ajax({
                url: "<?= url_to('database_export'); ?> ",
                type: 'GET',
                complete: function(data) {
                    if (data.status) {
                        toastr['success'](data.message, 'Success!', {
                            closeButton: true,
                            tapToDismiss: true,
                            progressBar: true
                        });
                    } else {
                        toastr['error'](data.message, 'Error!', {
                            closeButton: true,
                            tapToDismiss: true,
                            progressBar: true,
                        });
                    }
                }
            });
        });
    })
</script>