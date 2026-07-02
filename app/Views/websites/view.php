<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
	<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
 <!-- BEGIN: Content-->
  <!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <?php if (!empty($breadcrumb)) : ?>
                            <h2 class="content-header-title float-start mb-0"><?= $page_title; ?></h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <?php foreach ($breadcrumb as $item) : ?>
                                        <?php extract($item); ?>
                                        <li class="breadcrumb-item <?= $status; ?>">
                                            <?php if ($link) : ?>
                                                <a href="<?= $href; ?>"><?= $title; ?></a>
                                            <?php else : ?>
                                                <?= $title; ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        <?php else : ?>
                            <h2 class="content-header-title mb-0"><?= $page_title; ?></h2>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a class="btn btn-primary waves-effect waves-float waves-light" href="<?= base_url('domains/add'); ?>">
                    <i data-feather="globe"></i>
                    <span>Add Domain</span>
                </a>
            </div>
        </div>
        <div class="content-body">
            <div class="card" style="display: none;">
                <h5 class="card-header">Search Filter</h5>
                <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>

            <section id="ajax-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable table-responsive" style="margin: 10px;">
                                <table class="table table-striped table-bordered datatables-ajax" id="view-datatable">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Logo</th>
                                            <th style="width: 5%;">ID</th>
                                            <th style="width: 10%;">Code</th>
                                            <th style="width: 15%;">Web Name</th>
                                            <th style="width: 15%;">Domain</th>
                                            <th style="width: 20%;">Legal Name</th>
                                            <th style="width: 10%;">Type</th>
                                            <th style="width: 15%;">Payment Redirection</th>
                                            <th style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated here -->
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

<!-- Custom CSS -->
<style>
    .table thead th {
        vertical-align: middle;
        text-align: center;
    }
    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .btn {
        padding: 5px 10px;
    }
    .card {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .breadcrumb-wrapper {
        margin-bottom: 10px;
    }
</style>

    <!-- END: Content-->

            

            
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">

        $('.select2').select2();
        var table;
        table=$('#view-datatable').DataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax: {
                url: "<?= url_to('domains/get');?> ",
                type: 'GET',           
                data: function (d) {
                    
                },
                complete: function(data){
                    feather.replace();    
                }
            },
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

      function delete_data(id)
      {
         Swal.fire({
            title: 'Are you sure you want to delete?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
              confirmButton: 'btn btn-primary',
              cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
          }).then(function (result) {
            if (result.value) 
            {
                $.ajax({
                      url: "<?= base_url('domains/delete_record'); ?>",
                      type: 'GET',
                      dataType: 'json',
                      data:"id="+encodeURIComponent(id),
                      beforeSend: function() {

                      },
                      success: function (data) {
                         if(data.status){
                            toastr['success'](data.message, 'Success!', {
                              closeButton: true,
                              tapToDismiss: true,
                              progressBar: true
                            });
                            table.draw();
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

                      },
                      complete: function() {

                      }
                });
            }
          });                 
      }

      
</script>
<?= $this->endSection(); ?>