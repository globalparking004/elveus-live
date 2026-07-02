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
            <?php if (sizeof($breadcrumb) > 0) { ?>
              <h2 class="content-header-title float-start mb-0"><?= $page_title; ?></h2>
              <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                  <?php for ($i = 0; $i < sizeof($breadcrumb); $i++) {
                    extract($breadcrumb[$i]);
                  ?>
                    <?php if ($link) { ?>
                      <li class="breadcrumb-item <?= $status; ?>"><a href="<?= $href; ?>"><?= $title; ?></a></li>
                    <?php } else { ?>
                      <li class="breadcrumb-item <?= $status; ?>"><?= $title; ?> - <?= $domain['id']; ?></li>
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

      <div class="card" style="display: none;">
        <h5 class="card-header">Search Filter</h5>
        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
          <div class="col-md-4 user_role"></div>
          <div class="col-md-4 user_plan"></div>
          <div class="col-md-4 user_status"></div>
        </div>
      </div>



      <!-- Vertical Wizard -->
      <form action="<?= base_url('domains/update'); ?>" method="POST" id="frmsubmit">
        <?= csrf_field() ?>
        <section class="vertical-wizard">
          <div class="content-body">
            <div class="card">
            	<div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                <input type="hidden" name="id" name="id" value="<?= $domain['id']; ?>" />
            	    <div class="col-md-3">
                      <label class="form-label" for="short_code">Short Code</label>
                      <input class="form-control" type="text" name="short_code" id="short_code" value="<?= $domain['short_code'] ?>">
                  </div>
                  <div class="col-md-3">
                      <label class="form-label" for="airport_name">Airport Name</label>
                      <input class="form-control" type="text" name="airport_name" id="airport_name" value="<?= $domain['airport_name'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="web_name">Web Name</label>
                      <input class="form-control" type="text" name="web_name" id="web_name" value="<?= $domain['web_name'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="domain">Domain</label>
                      <input class="form-control" type="text" name="domain" id="domain" value="<?= $domain['domain'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="legal_name">Legal Name</label>
                      <input class="form-control" type="text" name="legal_name" id="legal_name" value="<?= $domain['legal_name'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="email">Email</label>
                      <input class="form-control" type="email" name="email" id="email" value="<?= $domain['email'] ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="type">Type</label>
                    <select class="select2" id="type" name="type">
                    	<option value="<?= $domain['type'] ?>" ><?= $domain['type'] ?></option>
                    	<option value="AIRPORT" >AIRPORT</option>
                    	<option value="PORT" >PORT</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="title">Title</label>
                      <input class="form-control" type="text" name="title" id="title" value="<?= $domain['title'] ?>">
                  </div>
                  <div class="col-md-3">
                      <label class="form-label" for="logo">Logo</label>
                      <input class="form-control" type="file" name="logo" id="logo">
                  </div>
                  <div class="col-md-3">
                      <label class="form-label" for="reviews">Reviews</label>
                      <input class="form-control" type="file" name="reviews" id="reviews">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="header_color">Header Color</label>
                      <input class="form-control" type="text" name="header_color" id="header_color" value="<?= $domain['header_color'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="footer_color">Footer Color</label>
                      <input class="form-control" type="text" name="footer_color" id="footer_color" value="<?= $domain['footer_color'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="google_analytics_id">Google Analytics ID</label>
                      <input class="form-control" type="text" name="google_analytics_id" id="google_analytics_id" value="<?= $domain['google_analytics_id'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="google_adwords_id">Google Adwords ID</label>
                      <input class="form-control" type="text" name="google_adwords_id" id="google_adwords_id" value="<?= $domain['google_adwords_id'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="google_conversion_event_id">Google Conversion Event ID</label>
                      <input class="form-control" type="text" name="google_conversion_event_id" id="google_conversion_event_id" value="<?= $domain['google_conversion_event_id'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="cur">Currency</label>
                      <input class="form-control" type="text" id="cur" name="cur" value="<?= $domain['cur'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="customer_service">Customer Service #</label>
                      <input class="form-control" type="text" id="customer_service" name="customer_service" value="<?= $domain['customer_service'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="address">Address</label>
                      <input class="form-control" type="text" id="address" name="address" value="<?= $domain['address'] ?>">
                  </div>
                   <div class="col-md-6">
                      <label class="form-label" for="address">Company ID</label>
                      <input class="form-control" type="text" id="company_id" name="company_id" value="<?= $domain['company_id'] ?>">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="footer">Footer</label>
                      <input class="form-control" type="text" id="footer" name="footer" value="<?= $domain['footer'] ?>">
                  </div>
                  <div class="col-md-12">
                      <label class="form-label" for="secret_key">Secret Key</label>
                      <input class="form-control" type="text" name="secret_key" id="secret_key" value="<?= $domain['secret_key'] ?>">
                  </div>
                  <div class="col-md-12">
                      <label class="form-label" for="publisher_key">Publisher Key</label>
                      <input class="form-control" type="text" name="publisher_key" id="publisher_key" value="<?= $domain['publisher_key'] ?>">
                  </div>
                   <div class="col-md-6">
                    <div class="mb-1">
                      <label class="form-label" for="terminals">Terminals</label>
                      <select class="form-select select2" id="terminals" name="terminals[]" multiple>
                          <option value="*">ALL</option>
                          <?php $selectedTerminals = explode(',', $domain['terminals']);
                            echo get_airport_terminals($selectedTerminals);  ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                      <label class="form-label" for="introduction">Payment Redirection</label>
                      <select class="form-select select2" id="" name="payment_redirection" id='payment_redirection' required>
                          <option value="self">Self</option>
                        <?php foreach($websites as $r){

                         
                        $name=$r->domain;
                        if($domain['payment_redirection']==$name)
                        {
                          echo "<option value='$name' selected >$name</option>";

                        }else{
                          echo "<option value='$name'>$name</option>";

                        }
                        }?>
                      </select>                 
                  </div>
                  <div class="col-md-3">
                      <label class="form-label">Status</label>
                      <select class="form-control" name="status">
                        <option value="1" <?= ($domain['status'] == 1)? 'selected':''?>>Active</option>
                        <option value="2" <?= ($domain['status'] == 2)? 'selected':''?>>In Active</option>
                      </select>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label" for="introduction">Introduction</label>
                      <textarea id="introduction" name="introduction"><?= $domain['introduction'] ?></textarea>
                  </div>
             
                 <!--  <div class="col-md-12">
                      <label class="form-label" for="footer">Footer</label>
                      <textarea id="footer" name="footer">?= $domain['footer'] ?></textarea>
                  </div> -->
                  <div class="col-md-12">
                      <label class="form-label" for="terms_condition">Terms Conditions</label>
                      <textarea id="terms_condition" name="terms_condition"><?= $domain['terms_conditions'] ?></textarea>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label" for="privacy_policy">Privacy Policy</label>
                      <textarea id="privacy_policy" name="privacy_policy"><?= $domain['privacy_policy'] ?></textarea>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label" for="why_choose">Why Choose</label>
                      <textarea id="why_choose" name="why_choose"><?= $domain['why_choose'] ?></textarea>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label" for="contact_us">Contact Us</label>
                      <textarea id="contact_us" name="contact_us"><?= $domain['contact_us'] ?></textarea>
                  </div>
              </div>
                <div class="modal-footer modal-footer-my">                      
                    <button class="btn btn-success" type="submit" id="btnsubmit">Submit</button>
                </div>
            </div>
          </div>
        </section>
      </form>
      <!-- /Vertical Wizard -->

    </div>
  </div>
</div>
<!-- END: Content-->



<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">
	// Ck editor
    CKEDITOR.replace('introduction');
    CKEDITOR.replace('why_choose');
    CKEDITOR.replace('terms_condition');
    CKEDITOR.replace('privacy_policy');
    CKEDITOR.replace('contact_us');
	// End



  $("#frmsubmit").submit(function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    var logoFile = $("#logo")[0].files[0];
    var reviewsFile = $("#reviews")[0].files[0];
    var introduction = CKEDITOR.instances['introduction'].getData();
    var why_choose = CKEDITOR.instances['why_choose'].getData();
    var terms_condition = CKEDITOR.instances['terms_condition'].getData();
    var privacy_policy = CKEDITOR.instances['privacy_policy'].getData();
    var contact_us = CKEDITOR.instances['contact_us'].getData();
    formData.append('why_choose', why_choose);
    formData.append('introduction', introduction);
    formData.append('terms_condition', terms_condition);
    formData.append('privacy_policy', privacy_policy);
    formData.append('contact_us', contact_us);
    formData.append('logo', logoFile);
    formData.append('reviews', reviewsFile);

    console.log('formData', formData);
    $.ajax({
      url: $(this).attr("action"),
      type: 'POST',
      dataType: 'json',
      data: formData,
      contentType: false,
      processData: false,
      async: true,
      cache: false,
      beforeSend: function() {
        $("#btnsubmit").attr("disabled", true);
      },
      success: function(data) {
        if (data.status) {
          toastr['success'](data.message, 'Success!', {
            closeButton: true,
            tapToDismiss: true,
            progressBar: true
          });
          location.reload();
        } else {
          if (data.errors) {
            $.each(data.errors, function(key, value) {
              toastr['error'](value, 'Error!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true,
              });
            });
          } else {
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
  });


  var validator = $(".form-crud").validate({
    rules: {
      'description': {
        required: vdstatus,
        minlength: 2
      },
      'capacity': {
        required: vdstatus,
        minlength: 1
      }
    },
    messages: {
      "description": {
        required: "Please enter your description",
        minlength: "description must be 2 char long"
      },
      "capacity": {
        required: "Please enter your capacity",
        minlength: "capacity must be 1 char long"
      }
    },
    submitHandler: function(form) {
      var formData = $(form).serialize();
      $.ajax({
        url: $(form).attr("action"),
        type: 'POST',
        dataType: 'json',
        data: formData,
        beforeSend: function() {
          $("#btnsubmit").attr("disabled", true);
        },
        success: function(data) {
          if (data.status) {
            toastr['success'](data.message, 'Success!', {
              closeButton: true,
              tapToDismiss: true,
              progressBar: true
            });
            table.draw();
            hideModal("add-xlarge");
            $(form)[0].reset();
          } else {
            if (data.errors) {
              validator.showErrors(data.errors);
            } else {
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

  function edit_data(id) {
    $.ajax({
      url: "<?= base_url('operators/get_record'); ?>",
      type: 'GET',
      dataType: 'json',
      data: "id=" + encodeURIComponent(id),
      beforeSend: function() {
        $("#form-crud-title").html("Modify Server");
        $(".clpassword").hide();
      },
      success: function(res) {
        if (res.status) {
          $('#status').val('');
          $("#id").val(id);
          $("#description").val(res.data.description);
          $("#capacity").val(res.data.capacity);
          $('#status').val(res.data.status);
          $('#status').trigger('change');
          $("#form-crud").attr("action", "<?= base_url('operators/update'); ?>");
          showModal("add-xlarge");
        }
      },
      error: function(xhr) {

      },
      complete: function() {

      }
    });

  }

  function delete_data(id) {
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
    }).then(function(result) {
      if (result.value) {
        $.ajax({
          url: "<?= base_url('operators/delete_record'); ?>",
          type: 'GET',
          dataType: 'json',
          data: "id=" + encodeURIComponent(id),
          beforeSend: function() {

          },
          success: function(data) {
            if (data.status) {
              toastr['success'](data.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              table.draw();
            } else {
              if (data.errors) {
                validator.showErrors(data.errors);
              } else {
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

  $("#add-xlarge").on("hidden.bs.modal", function() {
    $("#form-crud")[0].reset();
    $("#id").val('');
    $("#form-crud-title").html("Add Operators");
    $("#form-crud").attr("action", "<?= base_url('operators/save'); ?>");
  });


  const numberInputs = document.querySelectorAll(".number-input");
  numberInputs.forEach(function(input) {
    input.addEventListener("input", function() {
      const value = input.value;
      input.value = value.replace(/[^0-9.]/g, "");
    });
  });

  const alphanumericInputs = document.querySelectorAll(".alphanumeric-input");
  alphanumericInputs.forEach(function(input) {
    input.addEventListener("input", function() {
      const value = input.value;
      input.value = value.replace(/[^a-zA-Z0-9]/g, ""); // Allowing only alphanumeric characters
    });
  });
</script>
<?= $this->endSection(); ?>