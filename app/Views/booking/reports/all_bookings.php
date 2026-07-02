<?= $this->extend("layouts/base");
    $AUTH=session()->get('AUTH');
    $role_id=$AUTH['role_id'];
    $user_airport=$AUTH['airport'];
    $display="";
    if($role_id!="1")
    {
        $display="display:none;";
    } ?>
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
                <div class="card">
                    <h5 class="card-header">Search Filter </h5>
                    <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                
                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="airport">Airport</label>
                                <select class="form-select select2" id="airport" name="airport">
                                    <option value="">All</option>
                                    <?php $get_airports = get_airports();

                                        foreach ($get_airports as $code => $name) { ?>

                                        <option value="<?= $code; ?>"><?= $name; ?></option>

                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="airport">Website Type</label>
                                <select class="form-select select2" id="website_type" name="website_type">
                                    <option value="">All</option>
                                    <?php echo get_website_types();?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="status">Select Website</label>
                                <select class="form-select select2" id="website" name="website" required>
                                    <option value="">Select Website</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label">Operator</label>
                                <select class="form-select select2" id="operator" name="operator">
                                    <option value="">Select Operator</option>
                                        <?php foreach($agents as $r){
                                        $description=$r->description;
                                        $id=$r->id;
                                        echo "<option value='$id'>$description</option>";
                                        }?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 col-2">
                            <div class="mb-1">
                                <label class="form-label" for="band_name">Date From</label>
                                <input type="text" id="DateFrom" name="DateFrom" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2 col-2">
                            <div class="mb-1">
                                <label class="form-label" for="band_name">DateTo</label>
                                <input type="text" id="DateTo" name="DateTo" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="band_name">Status</label>
                                <select class="form-select select2" id="status" name="status">
                                    <option value="*">All</option>
                                    <option value="0">Pending</option>
                                    <?php if(strval($role_id)>1) { ?>
                                    <option selected value="1">Completed</option>
                                    <?php }else{ ?>
                                    <option value="1">Completed</option>
                                    <?php } ?>                                
                                    <option value="2">Cancelled</option>
                                    <option value="3">No Show</option>
                                </select>
                            </div>
                        </div>               



                        <div class="col-md-12 col-12">
                            <div class="mb-1">
                                <div class="custom-control custom-radio">
                                <?php if(strval($role_id)<=1) { ?>
                                  <input type="radio" value="booking_at" name="filter_date" class="custom-control-input" checked="">
                                  <label class="custom-control-label" for="filter_date">Booking</label>

                                  <input type="radio" value="departure_at" name="filter_date" class="custom-control-input">
                                  <label class="custom-control-label"  for="filter_date">Departure</label>
                                  <?php }else{ ?>
                                  <input type="radio" value="booking_at" name="filter_date" class="custom-control-input" >
                                  <label class="custom-control-label" for="filter_date">Booking</label>

                                  <input type="radio" value="departure_at" name="filter_date" class="custom-control-input" checked="">
                                  <label class="custom-control-label"  for="filter_date">Departure</label>
                                  <?php } ?> 

                                  <input type="radio" value="return_at" name="filter_date" class="custom-control-input">
                                  <label class="custom-control-label" for="filter_date">Return</label>

                                </div>                            
                            </div>
                        </div>

                        <div class="col-md-6 col-6">
                            <div class="mb-1">
                                <button type="submit" id="" onclick="search_data();" class="btn btn-primary">Search</button>
                                <?php if(strval($role_id)>1) { ?>
                                <!-- <button type="submit" id="" onclick="print_data();" class="btn btn-info">Print Bookings</button> -->
                                <button onclick="printTable()" class="btn btn-info">Print Bookings</button>

                                <button type="submit" id="" onclick="print_dards();" class="btn btn-danger">Print Cards</button>
                                <?php  } ?>
                            </div>
                        </div>
                    </div>
                </div>  

                <section id="ajax-datatable">
                    <div class="row">
                        <div class="col-12">
                        <div class="card">
                            <div class="card-datatable" style="margin: 10px;overflow-y:auto;">
                                <table class="datatables-ajax table table-responsive" id="view-datatable">
                                    <thead>
                                        <tr>
                                            <?php if($role_id>1) { ?>
                                                <!-- <th>Type</th> -->
                                                <th>Product Code</th>
                                                <th>Booking Ref</th>
                                                <th>Customer</th>
                                                <!-- <th>Contact Number</th> -->
                                                <th>Entry Date Time</th>
                                                <!-- <th>Entry Time</th> -->
                                                <th>Return Date Time</th>
                                                <!-- <th>Return Time</th> -->
                                                <th>Vehicle Make/Reg</th>
                                                <!-- <th>Vehicle Reg</th> -->
                                                <th>Passenger</th>

                                                <?php if($airport_type=="AIRPORT") { ?>
                                                    <th>Airport and Terminal</th>
                                                    <th>In Flight Number</th>
                                                    <th>Out Flight Number</th>
                                                    <th>Price</th>
                                                <?php }else{ ?>
                                                    <th>Cruise Details</th>
                                                    <th>Cruise Ship</th>
                                                    <th>Price</th>
                                                <?php } ?>    
                                                <!-- <th>Passenger</th> -->
                                                <th>Show</th>
                                                <th>Action</th>
                                            <?php }else{ ?>
                                                <th>Reference</th>
                                                <th>Website</th>
                                                <th>Airport/Car Park</th>
                                                <th>Customer Name</th>
                                                <th>Booked At</th>
                                                <th>Depart Date</th>
                                                <th>Return Date</th>
                                                <!-- <th>Registration</th> -->
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Type</th>
                                                <!-- <th>Promo Code</th> -->
                                                <th>Operator</th>
                                                <!-- <th>Passenger</th> -->
                                                <!-- <th>Show</th> -->
                                                <th>Action</th>
                                            <?php } ?>    
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                        <div class="col-2"></div>
                        <div class="col-4">
                            <div class="card">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <canvas id="doughnutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->
    <!-- Modals -->
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
                            <?php for ($r = 1; $r <= 31; $r++) {
                                $name = " $r Day"; ?>

                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="name">name</label>
                                            <input type="text" disabled value='<?= $name ?>' class="form-control" />
                                            <input type="hidden" id="<?= "name_" . $r; ?>" name="name[]" value='<?= $r ?>' class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="name">Rate</label>
                                            <input type="hidden" id="<?= "daily_rate_" . $r; ?>" name="daily_rate[]" class="form-control number-input" value="0" />
                                            <input type="text" disabled id="<?= "daily_rate_tmp" . $r; ?>" class="form-control number-input" value="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="name">Change To</label>
                                            <input type="text" id="<?= "day_rate_" . $r; ?>" name="day_rate[]" class="form-control number-input" value="0" />
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
                            $weekdays = get_weekdays();
                            foreach ($weekdays as $weekday) { ?>
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

    <div class="modal fade text-start" id="cancel_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <form class="form_cancel_booking" id="form-cancel_booking" action="<?= base_url('bookings/cancel_booking'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title">Cancel Booking</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                          
                <span id="cancel_booking_details">
                </span> 
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Cancel Booking and Refund £</button>
            </div>
            </form>
          </div>
        </div>
    </div>

    <div class="modal fade text-start" id="make_refund" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <form class="form_make_refund" id="form-make_refund" action="<?= base_url('bookings/make_refund'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title">Make a Refund</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                          
                <span id="make_refund_details">
                </span> 
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Refund £</button>
            </div>
            </form>
          </div>
        </div>
    </div>

    <div class="modal fade text-start" id="complete_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <form class="form_complete_booking" id="form-complete_booking" action="<?= base_url('bookings/update_status'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title">Complete Booking</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                          
            <span id="complete_booking_details">
            </span>       
            </div>
            </form>
          </div>
        </div>
    </div>


    <div class="modal fade text-start" id="edit_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <form class="form_edit_booking" id="form-edit_booking" action="<?= base_url('bookings/edit_booking'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title">  Amend Booking</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                          
            <span id="edit_booking_details">
            </span>       
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save Booking</button>
            </div>
            </form>
          </div>
        </div>
    </div>

    <div class="modal fade text-start" id="view_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <form class="form_view_booking" id="form-view_booking" action="<?= base_url('bookings/view_booking'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title">  View Booking</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                          
            <span id="view_booking_details">
            </span>       
            </div>
            </form>
          </div>
        </div>
    </div>

    <div class="modal fade text-start" id="move_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">  Move Booking</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                          
            <table width="100%">
                <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="move_booking_details">
                    
                </tbody>
            </table>
            </span>       
            </div>
          </div>
        </div>
    </div>

<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    const ctx4 = document.getElementById('pieChart');
    var pieChart = new Chart(ctx4, {
        type: 'pie',
        data: {
          labels: ['Total', 'Amount'],
          datasets: [
            {
              label: '',
              data: [0,0 ],
              backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                // 'rgb(153, 102, 255)'
                ],
            }
          ]
        },
        options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'All Bookings'
              }
            }
        }
    });
    const ctx4d = document.getElementById('doughnutChart');
    var doughnutChart=new Chart(ctx4d, {
        type: 'doughnut',
        data: {
          labels: ['Total', 'Amount'],
          datasets: [
            {
              label: '',
              data: [<?php echo $totals;?>, <?php echo $tot_amount;?>],
              backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                // 'rgb(153, 102, 255)'
                ],
            }
          ]
        },
        options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'All Bookings'
              }
            }
        }
    });
</script>
<script type="text/javascript">
    $('.select2').select2();
    $('#DateFrom').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });

    $('#DateTo').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });
    var table;
    table = $('#view-datatable').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('reports/bookings/get'); ?> ",
            type: 'GET',
            data: function(d) {
                    // d.Email = $("#Email").val();
                    d.DateFrom =  $("#DateFrom").val();
                    d.DateTo = $("#DateTo").val();
                    d.status = $("#status").val();
                    d.filter_date = $("input[name='filter_date']:checked").val();  
                    d.role_id ="<?= $role_id; ?>";        
                    d.website_type=$("#website_type").val();
                    d.website=$("#website").val();
                    d.airport=$("#airport").val();          
                    d.operator=$("#operator").val();          
            },
            complete: function(data) {
                // console.log('resp',data.responseJSON);
                pieChart.data.datasets[0].data = data.responseJSON.chartdata;
                pieChart.update();

                doughnutChart.data.datasets[0].data = data.responseJSON.chartdata;
                doughnutChart.update();

                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000],
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000],
        ],
        buttons: [
            // {
            //     extend: 'copyHtml5',
            //     exportOptions: {
            //       columns: ':visible',
            //       format: {
            //         body: function(data, row, column, node) {
            //           var text = node.textContent;
            //           return column === 10 ? text.replace(/Open$/, '').trim() : data;
            //         }
            //       }
            //     }
            // },
            {
                extend: 'excelHtml5',
                exportOptions: {
                  columns: ':visible',
                  format: {
                    body: function(data, row, column, node) {
                      var text = node.textContent;
                      return column === 10 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                  columns: ':visible',
                  format: {
                    body: function(data, row, column, node) {
                      var text = node.textContent;
                      return column === 10 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },

            // {
            //     extend: 'pdfHtml5',
            //     orientation: 'landscape',
            //     exportOptions: {
            //       columns: ':visible',
            //       format: {
            //         body: function(data, row, column, node) {
            //           var text = node.textContent;
            //           return column === 10 ? text.replace(/Open$/, '').trim() : data;
            //         }
            //       }
            //     }
            // },
        ],
        // "columns": [
        //     { "width": "5%" }, //product code
        //     { "width": "5%" }, // ref
        //     { "width": "5%" }, // customer
        //     { "width": "5%" }, // content
        //     { "width": "5%" }, 
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //     { "width": "5%" },
        //  ],
        "ordering": false
    });
    function search_data() 
    {
        table.draw();
    }
    // website type
    $('#website_type').on('change',function() {
        let val = $(this).val();
        let airport = $('#airport').val();
        console.log('val',val);
        $.ajax({
            url: "<?= url_to('reports/bookings/get_websites'); ?> ",
            type: 'GET',
            data:{val:val,airport:airport},
            // data: function(d) {
            //     d.website_type =  $(this).val();
            // },
            complete: function(data) {
                console.log('resp',data.responseText);
                $('#website').html(data.responseText);
            }
        });
    })
    $('#airport').on('change',function() {
        let airport = $(this).val();
        console.log('airport',airport);
        $.ajax({
            url: "<?= url_to('reports/bookings/get_websites'); ?> ",
            type: 'GET',
            data:{val:'',airport:airport},
            complete: function(data) {
                console.log('resp',data.responseText);
                $('#website').html(data.responseText);
            }
        });
    })
    // modals
    function show_status(id, currentStatus) 
    {
        Swal.fire({
            title: 'Are you sure you want to change this status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                
                var newStatus = (currentStatus === 1) ? 0 : 1;

                $.ajax({
                    url: "<?= base_url('bookings/show_status'); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: id,
                        show_status: newStatus
                    },
                    beforeSend: function() {
                        // You can add any code to be executed before the request is sent
                    },
                    success: function(data) {
                        if (data.status) {
                            toastr['success'](data.message, 'Success!', {
                                closeButton: true,
                                tapToDismiss: true,
                                progressBar: true
                            });
                            table.draw();
                            location.reload();
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
                        // Handle the error if needed
                    },
                    complete: function() {
                        // Code to be executed regardless of success or failure
                    }
                });
            }
        });
    }
    
    function show_booking_modal(ModalID,id)
    {   
    
        $("#view_booking").modal('hide');
        var form = document.getElementById("form-"+ModalID);
        // console.log(form);
        $.ajax({
            url: "<?= base_url('bookings/get_record'); ?>", 
            type: 'GET',
            dataType: 'json',
            data: "id=" + encodeURIComponent(id)+"&action="+ModalID,
            beforeSend: function() {                
                if (form) 
                {
                    var control = form.elements["id"];
                    control.value=id;
                }
            },
            success: function(data) {
                // console.log(data.details);
                $("#"+ModalID+"_details").html(data.details);
                 $(".flatpickr-date-time").flatpickr({
                    enableTime: true,
                    dateFormat: "d-M-Y H:i:s",
                    minDate: "today",
                    time_24hr: false
                });
                if(data.modal)
                 {
                    
                    showModal(ModalID);                          
                  
                 }else{

                    if(data.status){
                        toastr['success'](data.message, 'Success!', {
                          closeButton: true,
                          tapToDismiss: true,
                          progressBar: true
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

            },
            complete: function() {

            }
        });          
    }
</script>
<?= $this->endSection(); ?>