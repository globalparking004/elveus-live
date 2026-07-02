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
              <section id="page-account-settings">
                <div class="row">
                  <!-- right content section -->
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-body">

                          <!-- form -->
                          <form class="validate-form mt-2 form-general" id="form-general" action="<?= base_url('settings/save'); ?>">
                          	<?= csrf_field() ?>
                          	<input type="hidden" name="id" value="<?= id_en($result->id); ?>">
                            <div class="row">
                              <div class="col-12 col-sm-6">
                                <div class="mb-1">
                                  <label class="form-label" for="SMTPHost">SMTPHost</label>
                                  <input
                                    type="text"
                                    class="form-control"
                                    id="SMTPHost"
                                    name="SMTPHost"
                                    placeholder="SMTPHost"
                                    value="<?= $result->smtphost; ?>"
                                  />
                                </div>
                              </div>
                              <div class="col-12 col-sm-6">
                                <div class="mb-1">
                                  <label class="form-label" for="SMTPUser">SMTPUser</label>
                                  <input
                                    type="email"
                                    class="form-control"
                                    id="SMTPUser"
                                    name="SMTPUser"
                                    placeholder="SMTPUser"
                                    value="<?= $result->smtpuser; ?>"
                                  />
                                </div>
                              </div>
                              <div class="col-12 col-sm-6">
                                <div class="mb-1">
                                  <label class="form-label" for="SMTPPass">SMTPPass</label>
                                  <input
                                    type="text"
                                    class="form-control"
                                    id="SMTPPass"
                                    name="SMTPPass"
                                    placeholder="SMTPPass"
                                    value="<?= $result->smtppass; ?>"
                                  />
                                </div>
                              </div>
                              <div class="col-12 col-sm-6">
                                <div class="mb-1">
                                  <label class="form-label" for="SMTPPort">SMTPPort</label>
                                  <input
                                    type="text"
                                    class="form-control"
                                    id="SMTPPort"
                                    name="SMTPPort"
                                    placeholder="SMTPPort"
                                    value="<?= $result->smtpport; ?>"
                                  />
                                </div>
                              </div>
                              <div class="col-12 col-sm-6">
                                <div class="mb-1">
                                  <label class="form-label" for="bccEmail">BCC Email</label>
                                  <input
                                    type="email"
                                    class="form-control"
                                    id="bccEmail"
                                    name="bccEmail"
                                    placeholder="bccEmail"
                                    value="<?= $result->bccemail; ?>"
                                  />
                                </div>
                              </div>
                              <div class="col-12 col-sm-6">
                                <div class="mb-1">
                                  <label class="form-label" for="bccEmail">Product Capacity Limit</label>
                                  <input
                                    type="number"
                                    class="form-control"
                                    id="capacity_limit"
                                    name="capacity_limit"
                                    placeholder="Product Capacity Limit"
                                    value="<?= $result->capacity_limit; ?>"
                                  />
                                </div>
                              </div>
                              <div class="col-12" style="text-align: right;">
                                <button type="submit" class="btn btn-primary mt-2 me-1" id="btnsubmit-general">Save changes</button>                    
                              </div>
                            </div>
                          </form>
                          <!--/ form -->
                      </div>
                    </div>
                  </div>
                  <!--/ right content section -->
                </div>
              </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script>

    var validator=$(".form-general").validate({
          rules :{
            'SMTPHost':{
                required:vdstatus
            },
          }, messages :{
            "SMTPHost":"Please enter your SMTPHost",
          },submitHandler: function(form) { 
        var formData=$(form).serialize();
              $.ajax({
              url: $(form).attr("action"),
              type: 'POST',
              dataType: 'json',
              data:formData,
              beforeSend: function() {
                 $("#btnsubmit-general").attr("disabled", true);
              },
              success: function (data) {        
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
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
                $("#btnsubmit-general").attr("disabled", false);                        
              },
              complete: function() {
                $("#btnsubmit-general").attr("disabled", false);                
              }
          });       
        return false;
      }
      });

</script>
<?= $this->endSection(); ?>