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

        <div class="card" style="display: none;">
            <h5 class="card-header">Search Filter</h5>
            <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
              <div class="col-md-4 user_role"></div>
              <div class="col-md-4 user_plan"></div>
              <div class="col-md-4 user_status"></div>
            </div>
        </div>    



        <!-- Vertical Wizard -->
        <section class="vertical-wizard">
            <div class="bs-stepper vertical vertical-wizard-example">
                <div class="bs-stepper-header">
                  <div class="step" data-target="#contact-information-vertical" role="tab" id="contact-information-vertical-trigger">
                    <button type="button" class="step-trigger">
                      <span class="bs-stepper-box">
                        <i data-feather="dollar-sign" class="font-medium-3"></i>
                      </span>
                      <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Price Bands</span>
                      </span>
                    </button>
                  </div>
                  <div class="step" data-target="#key-information-vertical" role="tab" id="key-information-vertical-trigger">
                    <button type="button" class="step-trigger">
                      <span class="bs-stepper-box">
                        <i data-feather="file-text" class="font-medium-3"></i>
                      </span>
                      <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Ranges</span>
                      </span>
                    </button>
                  </div>
                  <div class="step" data-target="#availability-information-vertical" role="tab" id="availability-information-vertical-trigger">
                    <button type="button" class="step-trigger">
                      <span class="bs-stepper-box">
                        <i data-feather="delete" class="font-medium-3"></i>
                      </span>
                      <span class="bs-stepper-label">
                        <span class="bs-stepper-title">Close Outs</span>
                      </span>
                    </button>
                  </div>
                </div>


                <div class="bs-stepper-content">
                    <div id="contact-information-vertical" class="content" role="tabpanel" aria-labelledby="contact-information-vertical-trigger">
                        <div class="content-header">
                          <h5 class="mb-0">Price Bands : <a href="javascript:void(0);" onclick="add_bands();">Add New</a> | <a href="javascript:void(0);" onclick="import_bands();">Import</a></h5>
                          <div class="alert alert-info mt-1" style="padding:10px;">
                                 <div><i data-feather='info'></i> Create bands of prices to be assigned to ranges.</div>
                                 <div><i data-feather='info'></i> Each band requires a daily rate and a first day price. Specific lengths of stay can have prices assigned after which the daily rate will resume.</div>
                                 <div><i data-feather='info'></i> Bands can be assigned to any range.</div>
                          </div>         
                        </div>
                        <section id="ajax-datatable">
                            <div class="row">
                                <div class="col-12">
                                <div class="card">
                                    <div class="card-datatable" style="margin: 10px;">
                                    <table class="datatables-ajax table table-responsive" id="view-datatable-price-bands">
                                        <thead>
                                            <tr>
                                                <th>Name</th>                                
                                                <th>Daily Rate</th>
                                                <th>First Day Rate</th>
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
                    <div id="key-information-vertical" class="content" role="tabpanel" aria-labelledby="key-information-vertical-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">Ranges: <a href="javascript:void(0);" onclick="show_ranges('<?= $product_id; ?>');">Add New</a></h5>
                            <div class="alert alert-info mt-1" style="padding:10px;">
                                <div><i data-feather='info'></i> Create ranges of dates during which to use a specific price band. A single band can be assigned to a range or different bands for each/any day of the week within the range.</div>
                                <div><i data-feather='info'></i> One range can be set as the "default" and doesn't require any dates. This is the range used when no matching date ranges are found. We recommend this to be your highest price band.</div>
                            </div>
                            
                            <?= ($missingRanges)? '<div class="alert alert-danger mt-1" style="padding:10px;"><h3>Missing Ranges</h3>'.$missingRanges.'</div>':'';?>
                            
                        </div>
                        <section id="ajax-datatable">
                            <div class="row">
                                <div class="col-12">
                                <div class="card">
                                    <div class="card-datatable" style="margin: 10px;">
                                    <table class="datatables-ajax table table-responsive" id="view-datatable-range">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>From</th>                                
                                                <th>To</th>
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

                    <div id="availability-information-vertical" class="content" role="tabpanel" aria-labelledby="availability-information-vertical-trigger">
                        <div class="content-header">
                          <h5 class="mb-0">Close Outs: <a href="javascript:void(0);" onclick="show_close_out('<?= $product_id; ?>');">Add New</a></h5>
                          <div class="alert alert-info mt-1" style="padding:10px;">
                                 <div><i data-feather='info'></i> Create closeouts for days when cars cannot be accomodated.</div>
                                 <div><i data-feather='info'></i> A "Closed Out" type means a car cannot stay during this time regardless of when it arrives or departs.</div>
                                 <div><i data-feather='info'></i> A "No arrival/depature" means a car cannot enter or leave the car park during these dates but can arrive before and depart after.</div>
                          </div>
                        </div>
                        <section id="ajax-datatable">
                            <div class="row">
                                <div class="col-12">
                                <div class="card">
                                    <div class="card-datatable" style="margin: 10px;">
                                    <table class="datatables-ajax table table-responsive" id="view-datatable-close-out">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>From</th>                                
                                                <th>To</th>
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
        </section>
        <!-- /Vertical Wizard -->

        </div>
    </div>
</div>
<!-- END: Content-->        


<div class="modal fade text-start" id="add-band" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form class="form-crud" id="brand-form-crud" action="<?= base_url('products/add_band'); ?>">
        <input type="hidden" name="product_id" id="product_id" value="<?= $product_id ?>" />  
        <input type="hidden" name="band_id" id="band_id" value="" />
        <?= csrf_field() ?>        
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title">Add Band</h4>
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
                          <input type="text" id="band_daily_rate" name="band_daily_rate" class="form-control number-input" />
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="band_day_rate">First Day Rate</label>
                          <input type="text" id="band_day_rate" name="band_day_rate" class="form-control number-input" />
                        </div>
                    </div>     
                    </hr>               
                </div> 
                <div style="height: 500px;overflow-y:auto!important;overflow-x: hidden;">
            <?php for ($r=1;$r<=50;$r++) { $name=" $r Day"; ?>    
                           
                <div class="row">
                        <div class="col-md-4 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="name">Name</label>
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
        <form class="form-crud" id="range-form-crud" action="">
        <input type="hidden" name="range_product_id" id="range_product_id" value="<?= $product_id; ?>" />
        <input type="hidden" name="range_id" id="range_id" value="" />    
        <?= csrf_field() ?>        
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title-range">Add Ranges</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="dfrom">From</label>
                          <input
                            type="text"
                            id="dfrom"
                            name="dfrom"
                            class="form-control"
                            placeholder="" />                              
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="dto">To</label>
                          <input
                            type="text"
                            id="dto"
                            name="dto"
                            class="form-control"
                            placeholder="" /> 
                        </div>
                    </div> 
                    </hr>               
                </div> 
                <div>
                    <div class="row" id="def-row">
                      <div class="col-md-12 col-12">
                          <div class="mb-1">
                            <label class="form-label" for="def">Set all days to this band</label>
                            <select class="select2" id="def">
                                <?php
                                     echo "<option value='*'>NONE</option>";
                                     foreach ($bands as $band)
                                      {
                                         echo "<option value=`$band->id`>$band->name</option>";
                                      }
                                ?>
                            </select>
                          </div>
                      </div>
                    </div>
                    <?php 
                        $weekdays=get_weekdays();
                        foreach ($weekdays as $weekday) 
                        { ?>
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="mb-1">
                                      <label class="form-label" for="<?= $weekday ?>"><?= ucfirst($weekday); ?></label>
                                      <select class="select2 weekdays" name="<?= $weekday ?>" id="<?= $weekday ?>">
                                          <!-- <?php
                                               echo "<option value='*'>NONE</option>";
                                               foreach ($bands as $band)
                                                {
                                                   echo "<option value=`$band->id`>$band->name</option>";
                                                }
                                          ?> -->
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


<div class="modal fade text-start" id="add-close-out" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form class="form-crud" id="close-out-form-crud" action="<?= base_url('products/add_close_out'); ?>">
        <input type="hidden" name="close_out_product_id" id="close_out_product_id" value="<?= $product_id; ?>" />
        <input type="hidden" name="close_out_id" id="close_out_id" value="" />    
        <?= csrf_field() ?>        
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title-close-out">Add Close Out</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="close_out_type_id">Type of Close Out</label>
                            <select name="close_out_type_id" id="close_out_type_id" class="select2">
                                <option value="0">NONE</option>
                                <option value="1">No Arrival/Departure</option>
                                <option value="2">Closed Out</option>
                            </select>                            
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="close_out_from">From</label>
                          <input
                            type="text"
                            id="close_out_from"
                            name="close_out_from"
                            class="form-control"
                            placeholder="" />                              
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                          <label class="form-label" for="close_out_to">To</label>
                          <input
                            type="text"
                            id="close_out_to"
                            name="close_out_to"
                            class="form-control"
                            placeholder="" /> 
                        </div>
                    </div>             
                </div> 
                <div>
        </div>
        </div>
        <div class="modal-footer">                      
            <button type="submit" id="frmsubmit_close_out" class="btn btn-primary">Save</button>
        </div>
        </form>
      </div>
    </div>
</div>


<div class="modal fade text-start" id="import-band" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <form class="form-crud" id="import-band-form-crud" action="<?= base_url('products/import_band'); ?>"> 
        <input type="hidden" name="product_id" id="product_id" value="<?= $product_id ?>" />  
        <?= csrf_field() ?>        
        <div class="modal-header">
          <h4 class="modal-title">Import Band</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="mb-1">
                      <label class="form-label" for="band_name">name</label>
                      <input type="text" id="band_name" name="band_name" class="form-control" />
                    </div>
                </div>
                <div class="col-md-12 col-12">
                    <div class="mb-1">
                      <label class="form-label" for="band_daily_rate">Daily Rate</label>
                      <input type="text" id="band_daily_rate" name="band_daily_rate" class="form-control number-input" />
                    </div>
                </div>
                <div class="col-md-12 col-12">
                    <div class="mb-1">
                      <label class="form-label" for="band_day_rate">First Day Rate</label>
                      <input type="text" id="band_day_rate" name="band_day_rate" class="form-control number-input" />
                    </div>
                </div>
                <div class="col-md-12 col-12">
                    <div class="mb-1">
                      <label class="form-label">Import File</label>
                        <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">                           
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">                      
            <button type="submit" id="frmsubmit_import_band" class="btn btn-primary">Save</button>
        </div>
        </form>
      </div>
    </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">

    $('#dfrom').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
      });

    $('#dto').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
      });

    $('#close_out_from').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
      });

    $('#close_out_to').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });

        


    var table01;
    var product_id=$("#product_id").val();
    table01=$('#view-datatable-price-bands').DataTable({ 
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('products/get_rate_cards');?> ",
            type: 'GET',           
            data: function (d) {
                d.product_id=encodeURIComponent(product_id)
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


    var tblrange;
    var product_id=$("#product_id").val();
    tblrange=$('#view-datatable-range').DataTable({
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('products/get_ranges');?> ",
            type: 'GET',           
            data: function (d) {
                d.product_id=encodeURIComponent(product_id)
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

    
    function edit_range(id,product_id)
    {   
        // $("#def-row").hide();
        $('#def').val('*').trigger('change'); 
        $("#range_id").val(id);
        $.ajax({
              url: "<?= base_url('products/edit_range'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+id,
              beforeSend: function() {

              },
              success: function (data) {
                if(data.status)
                {   
                    var sunday=data.result.sunday;
                    var monday=data.result.monday;                        
                    var tuesday=data.result.tuesday;
                    var wednesday=data.result.wednesday;
                    var thursday=data.result.thursday;
                    var friday=data.result.friday;
                    var saturday=data.result.saturday;
                    var dfrom = data.result.dfrom;
                    var dto = data.result.dto;


                    $.ajax({
                      url: "<?= base_url('products/get_bands_ranges'); ?>",
                      type: 'GET',
                      dataType: 'json',
                      data:"id="+product_id,
                      beforeSend: function() {

                      },
                      success: function (data) {
                          var options = data.rate_cards;                 
                          var select = $('.weekdays');
                          select.empty();
                          var newOption = new Option("NONE","*");                  
                          select.append(newOption);
                          for (var option of options) 
                          { 
                            var newOption = new Option(option.text, option.id);             
                            select.append(newOption);
                          }
                        $("#form-crud-title-range").html("Modify Range");
                        showModal("add-ranges");
                        // $('#def').val(sunday).trigger('change');
                        $('#sunday').val(sunday).trigger('change');
                        $('#monday').val(monday).trigger('change');
                        $('#tuesday').val(tuesday).trigger('change');
                        $('#wednesday').val(wednesday).trigger('change');
                        $('#thursday').val(thursday).trigger('change');
                        $('#friday').val(friday).trigger('change');
                        $('#saturday').val(saturday).trigger('change');
                        $("#dfrom").val(dfrom);
                        $("#dto").val(dto);                            
                      },
                      error: function(xhr) {      

                      },
                      complete: function() {

                      }
                  });
                }
              },
              error: function(xhr) {      

              },
              complete: function() {

              }
          });
        
    }

    function show_ranges(id)
    {     
        $("#range_product_id").val(id);
        $("#def-row").show();
        $.ajax({
          url: "<?= base_url('products/get_bands_ranges'); ?>",
          type: 'GET',
          dataType: 'json',
          data:"id="+id,
          beforeSend: function() {

          },
          success: function (data) {
            var options = data.rate_cards;
                // console.log(options);
              var select = $('#def');
              select.empty();
              var newOption = new Option("NONE","*");
              select.append(newOption);
              for (var option of options) 
              { 
                var newOption = new Option(option.text, option.id);
                // if (option.id === 'option2') 
                // {
                //   newOption.selected = true;
                // }
                select.append(newOption);
              }
                
              var select = $('.weekdays');
              select.empty();
              var newOption = new Option("NONE","*");                  
              select.append(newOption);
              for (var option of options) 
              { 
                console.log(option)
                var newOption = new Option(option.text, option.id);
                // if (option.id === 'option2') 
                // {
                //   newOption.selected = true;
                // }
                select.append(newOption);
              }
            $("#form-crud-title-range").html("Add Range"); 
            showModal("add-ranges");
          },
          error: function(xhr) {      

          },
          complete: function() {

          }
        });
    }

    function add_bands()
    {
        $("#band_id").val('');
        $("#form-crud-title").html("Add Brand");
        showModal("add-band");
    }
    function import_bands()
    {
        showModal("import-band");
    }

    function show_bands(id)
    {
        //$("#product_id").val('');
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
                  $("#product_id").val(data.master[0].product_id);  
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
                $("#form-crud-title").html("Modify Brand");
                showModal("add-band");
                                 
              },
              error: function(xhr) {      

              },
              complete: function() {

              }
        });
    }

    function download_band(id)
    {
        $.ajax({
              url: "<?= base_url('products/download_band'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+encodeURIComponent(id),
              beforeSend: function() {

              },
              success: function (data) {
                toastr['success']('Download band successfully', 'Success!', {
                  closeButton: true,
                  tapToDismiss: true,
                  progressBar: true
                });
                                 
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
                table01.draw();
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

  $("#import-band-form-crud").submit(function(event) {
    event.preventDefault();
    // var formData=$(this).serialize();

    
    // Use FormData to handle file uploads
    var formData = new FormData(this);

    // Optional: Logging form data entries for verification
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    console.log("formData:", formData);

    $.ajax({
          url: $(this).attr("action"),
          type: 'POST',
          dataType: 'json',
          data:formData,
          contentType: false, // Required for FormData
          processData: false, // Prevent jQuery from converting the data
          beforeSend: function() {
             $("#frmsubmit_import_band").attr("disabled", true);
          },
          success: function (data) {
              if(data.status){
                toastr['success'](data.message, 'Success!', {
                  closeButton: true,
                  tapToDismiss: true,
                  progressBar: true
                });
                $("#import-band-form-crud")[0].reset();
                table01.draw();
                hideModal("import-band");
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
            $("#frmsubmit_import_band").attr("disabled", false);                        
          },
          complete: function() {
            $("#frmsubmit_import_band").attr("disabled", false);                
          }
      });
  });



  $("#range-form-crud").submit(function(event) {
    event.preventDefault();
    var formData=$(this).serialize();
    
    if($('#def').val() !== '*' || $('#sunday').val() !== '*'){
        $(this).attr('action','<?= base_url('products/add_ranges'); ?>');
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            dataType: 'json',
            data:formData,
            beforeSend: function() {
                 $("#frmsubmit_ranges").attr("disabled", true);
            },
            success: function (data) {
                  if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                    $("#range-form-crud")[0].reset();
                    tblrange.draw();
                    hideModal("add-ranges");
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
                $("#frmsubmit_ranges").attr("disabled", false);                        
            },
            complete: function() {
                $("#frmsubmit_ranges").attr("disabled", false);                
            }
        });
    }else{
        alert('Please select price band');
    }
        
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
        for(i=1;i<=50;i++)
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


      $("#add-ranges").on("hidden.bs.modal", function () {
         $("#range-form-crud")[0].reset();
         $("#range_id").val('');
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
                      url: "<?= base_url('products/delete_band'); ?>",
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
                            table01.draw();
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

      function delete_range(id)
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
                      url: "<?= base_url('products/delete_range'); ?>",
                      type: 'GET',
                      dataType: 'json',
                      data:"id="+id,
                      beforeSend: function() {

                      },
                      success: function (data) {
                         if(data.status){
                            toastr['success'](data.message, 'Success!', {
                              closeButton: true,
                              tapToDismiss: true,
                              progressBar: true
                            });
                            tblrange.draw();
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

      $('#def').on('change', function() 
      {
        var selectedValue = $(this).val();
        selectedValue= selectedValue.replace(/\D/g, '');
        // console.log('selectedValue',selectedValue);
        $('.weekdays').val(selectedValue).trigger('change');
      });




      function show_close_out(id)
      {
         $("#form-crud-title-close-out").html("Add Close Out");
         $("#close_out_id").val("");
         showModal("add-close-out");
      }


 


    var tblclose;
    var product_id=$("#product_id").val();
    tblclose=$('#view-datatable-close-out').DataTable({
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('products/get_close_outs');?> ",
            type: 'GET',           
            data: function (d) {
                d.product_id=encodeURIComponent(product_id)
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


    $("#close-out-form-crud").submit(function(event) {
    event.preventDefault();
    var formData=$(this).serialize();
    $.ajax({
          url: $(this).attr("action"),
          type: 'POST',
          dataType: 'json',
          data:formData,
          beforeSend: function() {
             $("#frmsubmit_ranges").attr("disabled", true);
          },
          success: function (data) {
              if(data.status){
                toastr['success'](data.message, 'Success!', {
                  closeButton: true,
                  tapToDismiss: true,
                  progressBar: true
                });
                $("#close-out-form-crud")[0].reset();
                tblclose.draw();
                hideModal("add-close-out");
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
            $("#frmsubmit_close_out").attr("disabled", false);                        
          },
          complete: function() {
            $("#frmsubmit_close_out").attr("disabled", false);                
          }
      });
  });


  function edit_close_out(id)
  {         
        $("#close_out_id").val(id);
        $.ajax({
              url: "<?= base_url('products/edit_close_out'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+id,
              beforeSend: function() {

              },
              success: function (data) {
                console.log(data);
                if(data.status)
                {
                    console.log(data.result.close_out_type_id);
                    $("#close_out_product_id").val(data.result.product_id);
                    $("#close_out_id").val(data.result.id);
                    $("#close_out_from").val(data.result.close_out_from);
                    $("#close_out_to").val(data.result.close_out_to);
                    $('#close_out_type_id').val(data.result.close_out_type_id).trigger('change');
                    $("#form-crud-title-close-out").html("Modify Close Out");                    
                    showModal("add-close-out");
                }else{
                    toastr['error'](data.message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true,
                    });
                }
              },
              error: function(xhr) {      

              },
              complete: function() {

              }
          });
  }


  function delete_close_out(id)
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
                      url: "<?= base_url('products/delete_close_out'); ?>",
                      type: 'GET',
                      dataType: 'json',
                      data:"id="+id,
                      beforeSend: function() {

                      },
                      success: function (data) {
                         if(data.status){
                            toastr['success'](data.message, 'Success!', {
                              closeButton: true,
                              tapToDismiss: true,
                              progressBar: true
                            });
                            tblclose.draw();
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