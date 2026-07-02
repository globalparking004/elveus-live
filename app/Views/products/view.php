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
                    <a class="btn btn-primary waves-effect waves-float waves-light" href="<?= base_url('products/add'); ?>">
                      <i data-feather='file-plus'></i>
                      <span>Add Products</span>
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
                        <div class="card-datatable" style="margin: 10px;">
                        <table class="datatables-ajax table table-responsive" id="view-datatable">
                            <thead>
                            <tr>
                                <!-- <th>Logo</th>                                 -->
                                <th>Created at</th>                                
                            	<th>Logo</th>                                
                                <th>Code</th>
                                <th>Name</th>
                                <th>Airport</th>                                
                                <!-- <th>API-Airport</th> -->
                                <th>Linked</th>
                                <th>Commission</th>
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


<div class="modal fade text-start" id="add-band" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form class="form-crud" id="brand-form-crud" action="<?= base_url('products/add_band'); ?>">
        <input type="hidden" name="product_id" id="product_id" value="" />  
        <input type="hidden" name="band_id" id="band_id" value="" />
        <?= csrf_field() ?>        
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title">Add Rate Card</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="band_name">name</label>
                          <input type="text" id="band_name" name="band_name" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="band_daily_rate">Daily Rate</label>
                          <input type="text" id="band_daily_rate" name="band_daily_rate" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="band_day_rate">First Day Rate</label>
                          <input type="text" id="band_day_rate" name="band_day_rate" class="form-control" />
                        </div>
                    </div>     
                    </hr>               
                </div> 
                <div style="height: 500px;overflow-y:auto!important;overflow-x: hidden;">
            <?php for ($r=1;$r<=31;$r++) { $name=" $r Day"; ?>    
                           
                <div class="row">
                        <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="name">name</label>
                          <input type="text" disabled value='<?= $name ?>' class="form-control" />
                          <input type="hidden" id="<?= "name_".$r; ?>" name="name[]" value='<?= $r ?>' class="form-control" />
                        </div>
                      </div>
                      <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="name">Rate</label>
                          <input type="hidden" id="<?= "daily_rate_".$r; ?>" name="daily_rate[]" class="form-control number-input" value="0" />
                          <input type="text" disabled id="<?= "daily_rate_tmp".$r; ?>" class="form-control number-input" value="0" />
                        </div>
                      </div>
                      <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="name">Change To</label>
                          <input type="text" id="<?= "day_rate_".$r; ?>" name="day_rate[]" class="form-control number-input" value="0" />
                        </div>
                      </div>
                </div>
            <?php } ?>
        </div>
        </div>
        <div class="modal-footer">                      
            <button type="submit" id="frmsubmit" class="btn btn-primary">Save</button>
        </div>
        </form>
      </div>
    </div>
  </div>


  <div class="modal fade text-start" id="add-ranges" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form class="form-crud" id="brand-form-crud" action="<?= base_url('products/add_ranges'); ?>">
        <input type="hidden" name="range_product_id" id="range_product_id" value="" />  
        <?= csrf_field() ?>        
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title">Add Ranges</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="dfrom">From</label>
                          <input type="date" id="dfrom" name="dfrom" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="dto">To</label>
                          <input type="date" id="dto" name="dto" class="form-control" />
                        </div>
                    </div> 
                    </hr>               
                </div> 
                <div>
                    <?php 
                        $weekdays=get_weekdays();
                        foreach ($weekdays as $weekday) 
                        { ?>
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="mb-1">
                                      <label class="form-label" for="<?= $weekday ?>"><?= ucfirst($weekday); ?></label>
                                      <select class="select2 weekdays" name="<?= $weekday ?>" id="<?= $weekday ?>">
                                          
                                      </select>
                                    </div>
                                </div>
                            </div>
                    <?php } ?>
        </div>
        </div>
        <div class="modal-footer">                      
            <button type="submit" id="frmsubmit_ranges" class="btn btn-primary">Save</button>
        </div>
        </form>
      </div>
    </div>
  </div>

<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">
        var table;
        table=$('#view-datatable').DataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax: {
                url: "<?= url_to('products/get');?> ",
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
                      url: "<?= base_url('products/delete_record'); ?>",
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

      function show_ranges(id)
      {
            $("#product_id").val(id);
        $.ajax({
              url: "<?= base_url('products/get_ranges'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+encodeURIComponent(id),
              beforeSend: function() {

              },
              success: function (data) {
                const options = data.rate_cards;
                    console.log(options);
                  const select = $('#sunday');
                  select.empty();
                  for (const option of options) 
                  { 
                    console.log(option.text);
                    console.log("---------------------");
                    console.log(option.id);
                    const newOption = new Option(option.text, option.id);
                    // if (option.id === 'option2') 
                    // {
                    //   newOption.selected = true;
                    // }
                    select.append(newOption);
                  }
                showModal("add-ranges");
              },
              error: function(xhr) {      

              },
              complete: function() {

              }
          });
      }

      function show_bands(id)
      {
        $("#product_id").val(id);
        $.ajax({
              url: "<?= base_url('products/get_band'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+encodeURIComponent(id),
              beforeSend: function() {

              },
              success: function (data) {
                if(data.status)
                {
                  $("#band_id").val(data.master[0].id);
                  $("#band_name").val(data.master[0].name);
                  $("#band_daily_rate").val(data.master[0].daily_rate);
                  $("#band_day_rate").val(data.master[0].day_rate);
                    var _counter=1;
                    for(i=0;i<data.detials.length;i++)
                    {
                        $("#daily_rate_"+_counter).val(data.detials[i].daily_rate);
                        $("#daily_rate_tmp"+_counter).val(data.detials[i].daily_rate);
                        $("#day_rate_"+_counter).val(data.detials[i].day_rate);
                        console.log(data.detials[i].daily_rate + "=>" + data.detials[i].day_rate);
                        _counter++;
                    }
                }
                showModal("add-band");
                                 
              },
              error: function(xhr) {      

              },
              complete: function() {

              }
          });

        
      }

  $("#brand-form-crud").submit(function(event) {
    event.preventDefault();
    var formData=$(this).serialize();
    $.ajax({
          url: $(this).attr("action"),
          type: 'POST',
          dataType: 'json',
          data:formData,
          beforeSend: function() {
             $("#frmsubmit").attr("disabled", true);
          },
          success: function (data) {
              if(data.status){
                toastr['success'](data.message, 'Success!', {
                  closeButton: true,
                  tapToDismiss: true,
                  progressBar: true
                });
                $("#brand-form-crud")[0].reset();
                hideModal("add-band");
              }else{
                if(data.errors) {
                    $.each(data.errors,function(key, value) {
                            toastr['error'](value, 'Error!', {
                              closeButton: true,
                              tapToDismiss: true,
                              progressBar: true,
                            });
                        });
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
            $("#frmsubmit").attr("disabled", false);                        
          },
          complete: function() {
            $("#frmsubmit").attr("disabled", false);                
          }
      });
  });


      const numberInputs = document.querySelectorAll(".number-input");
      numberInputs.forEach(function (input) {
        input.addEventListener("input", function () {
          const value = input.value;
          input.value = value.replace(/[^0-9.]/g, "");
        });
      });

    const inputElement1 = document.getElementById("band_daily_rate");
    inputElement1.addEventListener('focusout', function(event) {
        var band_daily_rate=$("#band_daily_rate").val();
        console.log(band_daily_rate);
        calculate_rates();
    });

    const inputElement2 = document.getElementById("band_day_rate");
    inputElement2.addEventListener('focusout', function(event) {
        var band_day_rate=$("#band_day_rate").val();
        console.log(band_day_rate);
        calculate_rates();
    });


    function calculate_rates()
    {   
        var band_id=$("#band_id").val();
        if(parseInt(band_id)>0)
        {
            return;
        }
        var daily_rate=$("#band_daily_rate").val();
        var first_day_rate=$("#band_day_rate").val();
        var previous_data_rate=0;
        for(i=1;i<=31;i++)
        {   if(i==1)
            {   var daily_rate_current=parseFloat(first_day_rate);
                daily_rate_current = Number.isNaN(daily_rate_current) ? 0 : daily_rate_current;
                $("#daily_rate_"+i).val(daily_rate_current);
                $("#day_rate_"+i).val(daily_rate_current);
                $("#daily_rate_tmp"+i).val(parseFloat(daily_rate_current));
                previous_data_rate=daily_rate_current;
            }else{
                var daily_rate_current=parseFloat(previous_data_rate)+parseFloat(daily_rate);
                daily_rate_current = Number.isNaN(daily_rate_current) ? 0 : daily_rate_current;
                $("#daily_rate_"+i).val(daily_rate_current);
                $("#day_rate_"+i).val(daily_rate_current);
                $("#daily_rate_tmp"+i).val(parseFloat(daily_rate_current));
                previous_data_rate=daily_rate_current;
            }
        }
       
    }


      $("#add-band").on("hidden.bs.modal", function () {
         $("#brand-form-crud")[0].reset();
         $("#band_id").val('');
      });

</script>
<?= $this->endSection(); ?>