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
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-xlarge">
                      <i data-feather='user-plus'></i>
                      <span>Add Driver</span>
                    </button>
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
                                  <th>Airport</th>
                                  <th>Name</th>
                                  <th>Phone</th>
                                  <th>Action</th>
                              </tr>
                              </thead>
                              <tbody>
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
    <div class="modal fade text-start" id="add-xlarge" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <form class="form-crud" id="form-crud" action="<?= base_url('drivers/save'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title" id="form-crud-title">Add Driver</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                  <div class="col-md-6 col-12">
                    <div class="mb-1">
                      <label class="form-label">Name</label>
                      <input
                        type="text"
                        id="name"
                        class="form-control"
                        placeholder="Name"
                        name="name"
                      />
                    </div>
                  </div>                             
                  <div class="col-md-6 col-12">
                    <div class="mb-1">
                      <label class="form-label" for="phone">Phone</label>
                      <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" />
                    </div>
                  </div>
                  <div class="col-md-12 col-12">
                    <div class="mb-1">
                      <label class="form-label" for="airport">Airport</label>
                      <select class="form-select select2" id="airport" name="airport">
                          <option value="*">ALL</option>
                          <?php 
                            $airports=get_airports(); 
                            foreach ($airports as $code => $name) 
                            {
                              echo "<option value='$code'>$name</option>";
                            }
                          ?>
                      </select>
                    </div>
                  </div>
                                                
                </div>
            </div>
            <div class="modal-footer">                      
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" id="btnsubmit" class="btn btn-primary">Continue</button>
            </div>
          </form>
        </div>
      </div>
    </div>

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
            url: "<?= url_to('drivers/get');?> ",
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


    var validator = $(".form-crud").validate({
      rules: {
        name: {
          required: vdstatus,
          minlength: 2,
        },
        airport: {
          required: vdstatus,
        },
      },
      messages: {
        name: {
          required: "Please enter your Name",
          minlength: "Name must be at least 2 characters long",
        },
        airport: {
          required: "Please select an airport",
        },
      },
      submitHandler: function(form) { 
          var formData=$(form).serialize();
          console.log('formData', formData);
          $.ajax({
            url: $(form).attr("action"),
            type: 'POST',
            dataType: 'json',
            data:formData,
            beforeSend: function() {
               $("#btnsubmit").attr("disabled", true);
            },
            success: function (data) {
              console.log('res:', data);    
                if(data.status){
                  toastr['success'](data.message, 'Success!', {
                    closeButton: true,
                    tapToDismiss: true,
                    progressBar: true
                  });
                  table.draw();
                  hideModal("add-xlarge");
                  $(form)[0].reset();
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
              $("#btnsubmit").attr("disabled", false);                        
            },
            complete: function() {
              $("#btnsubmit").attr("disabled", false);                
            }
          });       
          return false;
        }
    });

    function edit_data(id)
    {     
        $.ajax({
            url: "<?= base_url('drivers/get_record'); ?>",
            type: 'GET',
            dataType: 'json',
            data:"id="+encodeURIComponent(id),
            beforeSend: function() {
              $("#form-crud-title").html("Modify Driver");
            },
            success: function (res) {
              console.log(res.data);
              if(res.status)
              {                  
                 $("#id").val(id);
                 $("#name").val(res.data.name);
                 $("#phone").val(res.data.phone);
                 $("#airport").val(res.data.airport);

                 $('#airport').trigger('change');

                 $("#form-crud").attr("action","<?= base_url('drivers/update'); ?>");
                 showModal("add-xlarge");
              }                
            },
            error: function(xhr) {      

            },
            complete: function() {

            }
        });

    } 

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
                    url: "<?= base_url('drivers/delete'); ?>",
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

    $("#add-xlarge").on("hidden.bs.modal", function () {
       $("#form-driver")[0].reset();
       $("#id").val('');
       $("#airport").val("*");
       $('#airport').trigger('change');
       $("#form-crud-title").html("Add Driver");
       $("#form-driver").attr("action","<?= base_url('drivers/save'); ?>");
    });

</script>
<?= $this->endSection(); ?>