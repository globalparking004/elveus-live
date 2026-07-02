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
   

            <section id="ajax-datatable">
                <div class="row">
                    <div class="col-12">
                    <div class="card">
                        <div class="card-datatable" style="margin: 10px;">
                        <table class="datatables-ajax table table-responsive" id="view-datatable">
                            <thead>
                            <tr>                                
                                <th>Created At</th>
                                <th>ID</th>
                                <th>Action</th>
                                <th>Detail</th>
                            </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($data as $key => $log) {
                                echo'<tr>
                                <td>'.date("d-m-Y", strtotime($log->created_at)).'</td>
                                <td>'.$log->reference.'</td>
                                <td>'.$log->action.'</td>
                                <td>'.$log->details.'</td>
                                </tr>';
                              }?>
                            </tbody>   
                        </table>
                        </div>
                    </div>
                    </div>
                </div>
            </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->


<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">

    var table;
    table=$('#view-datatable').DataTable({
        processing: true,
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "columnDefs": [{
          "targets": 0,
          "orderable": false
        }],
        "lengthMenu": [
                [10, 25, 50, 100, 200, 300, 400, 500],
                [10, 25, 50, 100, 200, 300, 400, 500],
            ]
    });

</script>
<?= $this->endSection(); ?>