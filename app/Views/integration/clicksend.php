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
                      <i data-feather='message-circle'></i>
                      <span>Quick SMS</span>
                    </button>
                </div>
            </div>
            <div class="content-body">   

              <section id="ajax-datatable">
                  <div class="row">
                      <div class="col-12">
                      <div class="card">
                          <div class="card-datatable" style="margin: 10px;overflow-x: scroll;">
                          <table class="datatables-ajax table table-responsive" id="view-datatable">
                              <thead>
                              <tr>
                                  <th style="width: 10%">Date</th>
                                  <th>From</th>
                                  <th>To</th>
                                  <th>Status</th>
                                  <th>Body</th>
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
          <form class="form-crud" id="form-crud" action="<?= base_url('clicksend/sms_sent1'); ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
              <h4 class="modal-title" id="form-crud-title">Quick SMS</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">                            
                  <div class="col-md-6 col-6">
                    <div class="mb-1">
                      <label class="form-label" for="phone">Phone</label>
                      <input type="text" id="phone" class="form-control" placeholder="Enter Mobile number" name="phone" />
                    </div>
                  </div>
                  <div class="col-md-6 col-6">
                    <div class="mb-1">
                      <label class="form-label" for="template">Template</label>
                      <select class="form-select select2" id="template" name="template">
                          <option value="">Select Template</option>
                          <?php if($templates):
                            foreach ($templates as $key => $temp) 
                            {
                              $template_id = $temp->template_id;
                              $template_name = $temp->template_name;
                              echo "<option value='$template_id'>$template_name</option>";
                            }
                            endif;
                          ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12 col-12">
                    <div class="mb-1">
                        <label>Description</label>
                        <textarea name="message" id="message" class="form-control" rows="10"></textarea>
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
        // select: true,
        ajax: {
            url: "<?= url_to('clicksend/get');?> ",
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
        "order": [[0, "desc"]],
        "columns": [
            { "orderable": true },
            { "orderable": true },
            { "orderable": true },
            { "orderable": true },
            { "orderable": true },
        ],
        "lengthMenu": [
                [20, 50, 100, 200, 300, 400, 500],
                [20, 50, 100, 200, 300, 400, 500],
            ]
    });
    // select template
    $('#template').on('change', function() 
    {
      if ($(this).val()) 
      {
        $('#message').val('');
        $.ajax({
            url: "<?= url_to('clicksend/get_template'); ?> ",
            type: 'GET',
            data: { template_id : $(this).val() },
            complete: function (data) {
                console.log('response',data.responseJSON.data);
                $('#message').val(data.responseJSON.data.body);
            }
        });
      }
    });

    var validator = $(".form-crud").validate({
      rules: {
        message: {
          required: vdstatus,
          minlength: 2,
        },
        phone: {
          required: vdstatus,
        },
      },
      messages: {
        message: {
          required: "Please enter your message",
          minlength: "Message must be at least 2 characters long",
        },
        phone: {
          required: "Please enter phone number",
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
                  hideModal("add-xlarge");
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


    $("#add-xlarge").on("hidden.bs.modal", function () {
       $("#form-crud")[0].reset();
       $("#to").val('');
       $("#template").val("");
       $('#template').trigger('change');
       $("#form-crud-title").html("Quick SMS");
       // $("#form-crud").attr("action","<?= base_url('clicksend/save'); ?>");
    });

</script>
<?= $this->endSection(); ?>