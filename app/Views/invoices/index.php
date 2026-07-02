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
                      <span>Add User</span>
                    </button>
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
                        <div class="card-datatable" style="margin: 10px;">
                        <table class="datatables-ajax table table-responsive" id="view-datatable">
                            <thead>
                            <tr>                                
                                <th>Created At</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
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

            <div class="modal fade text-start" id="reset-password" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form class="form-reset-password" id="form-reset-password" action="<?= base_url('users/reset_password'); ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="user_id" id="user_id" value="" />
                  <div class="modal-header">
                    <h4 class="modal-title">Reset Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">                          
                          <div class="row">
                            <div class="col-md-12 col-12">
                              <div class="mb-1">
                                  <label class="form-label" for="password">Password</label>
                                  <div class="input-group input-group-merge form-password-toggle">
                                    <input class="form-control form-control-merge" id="password" type="password" name="password" placeholder="············" aria-describedby="password" tabindex="2"/><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                  </div>
                              </div>
                            </div>                                                           
                          </div>
                  </div>
                  <div class="modal-footer">
                      <button type="submit" id="btnsubmit-reset" class="btn btn-primary">Continue</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="modal fade text-start" id="add-xlarge" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content">
                    <form class="form-crud" id="form-crud" action="<?= base_url('users/save'); ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="id" value="" />
                    <div class="modal-header">
                      <h4 class="modal-title" id="form-crud-title">Add User</h4>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">                          
                            <div class="row">
                              <div class="col-md-6 col-12">
                                <div class="mb-1">
                                  <label class="form-label" for="first_name">First Name</label>
                                  <input
                                    type="text"
                                    id="first_name"
                                    class="form-control"
                                    placeholder="First Name"
                                    name="first_name"
                                  />
                                </div>
                              </div>
                              <div class="col-md-6 col-12">
                                <div class="mb-1">
                                  <label class="form-label" for="last_name">Last Name</label>
                                  <input
                                    type="text"
                                    id="last_name"
                                    class="form-control"
                                    placeholder="Last Name"
                                    name="last_name"
                                  />
                                </div>
                              </div>
                              <div class="col-md-6 col-12">
                                <div class="mb-1">
                                  <label class="form-label" for="email">Email</label>
                                  <input
                                    type="email"
                                    id="email"
                                    class="form-control"
                                    name="email"
                                    placeholder="Email"
                                  />
                                </div>
                              </div>                              
                              <div class="col-md-6 col-12">
                                <div class="mb-1">
                                  <label class="form-label" for="phone">Phone</label>
                                  <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" />
                                </div>
                              </div>
                              <div class="col-md-6 col-12 clpassword">
                                <div class="mb-1">
                                  <label class="form-label" for="password">Password</label>
                                  <div class="input-group input-group-merge form-password-toggle">
                                    <input class="form-control form-control-merge" id="password" type="password" name="password" placeholder="············" aria-describedby="password" tabindex="2"/><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 col-12">
                                <div class="mb-1">
                                  <label class="form-label" for="status">Status</label>
                                  <select class="form-select select2" id="status" name="status">
                                      <option value="inactive">Inactive</option>
                                      <option value="active">Active</option>
                                      <option value="pending">Pending</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6 col-12">
                                <div class="mb-1">
                                  <label class="form-label" for="role_id">Role</label>
                                  <select class="form-select select2" id="role_id" name="role_id">
                                        <?php 
                                        for ($i=0; $i<sizeof($roles); $i++) 
                                        { 
                                            echo "<option value='".$roles[$i]->id."'>".$roles[$i]->description."</option>";
                                        }
                                        ?>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6 col-12" style="display:none;">
                                <div class="mb-1">
                                  <label class="form-label" for="type">Account Type</label>
                                  <select class="form-select select2" id="type" name="type">
                                      <option value="Staff">Staff</option>
                                      <option value="Cleint">Cleint</option>
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
                url: "<?= url_to('users/get');?> ",
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

        $('#role_id_x').select2({
            placeholder: 'Select Role',
            allowClear: true,
            ajax: {
                url: "<?= base_url(); ?>/users/get_roles",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        searchTerm: data.term
                    };
                },
                processResults: function (response) {
                    return {
                        results:response
                    };
                },
                cache: true
            }
        });

    var validator=$(".form-crud").validate({
          rules :{
            'first_name':{
                required:vdstatus,
                minlength:2
            },
            'email':{
                required:vdstatus,
                email:vdstatus
            },
            'password':{
              required:vdstatus,
              minlength:5
            }
          }, messages :{
            "first_name":{
              required:"Please enter your first name",
              minlength:"first name must be 2 char long"
            },
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



      var validator=$(".form-reset-password").validate({
          rules :{
            'password':{
              required:vdstatus,
              minlength:5
            }
          }, messages :{
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
                 $("#btnsubmit-reset").attr("disabled", true);
              },
              success: function (data) {        
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                    hideModal("reset-password");
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
                $("#btnsubmit-reset").attr("disabled", false);                        
              },
              complete: function() {
                $("#btnsubmit-reset").attr("disabled", false);                
              }
          });       
        return false;
      }
      });

    


      function edit_data(id)
      {     
          $.ajax({
              url: "<?= base_url('users/get_record'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+encodeURIComponent(id),
              beforeSend: function() {
                $("#form-crud-title").html("Modify User");
                $(".clpassword").hide();
              },
              success: function (res) {
                if(res.status)
                {  
                   $('#status').val('');
                   $('#role_id').val('');
                   $('#type').val('');                  
                   $("#id").val(id);
                   $("#first_name").val(res.data.first_name);
                   $("#last_name").val(res.data.last_name);
                   $("#email").val(res.data.email);
                   $("#phone").val(res.data.phone);
                   $("#status").val(res.data.status);
                   $("#role_id").val(res.data.role_id);
                   $("#type").val(res.data.type);

                   $('#status').trigger('change');
                   $('#role_id').trigger('change');
                   $('#type').trigger('change');
                   $("#form-crud").attr("action","<?= base_url('users/update'); ?>");
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
                      url: "<?= base_url('users/delete_record'); ?>",
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
         $("#form-crud")[0].reset();
         $("#id").val('');
         $("#form-crud-title").html("Add User");
         $("#form-crud").attr("action","<?= base_url('users/save'); ?>");
         $(".clpassword").show();
      });

      $("#reset-password").on("hidden.bs.modal", function () {
         $("#form-reset-password")[0].reset();
         $("#user_id").val('');
      });


      function reset_password(id)
      {   
          $("#user_id").val(id);    
          showModal("reset-password");
      }

</script>
<?= $this->endSection(); ?>