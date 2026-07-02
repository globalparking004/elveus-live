<?php 
    $AUTH=session()->get('AUTH');
    $role_id=$AUTH['role_id'];
    $role_name=$AUTH['role_name'];
    $user_airport=$AUTH['airport'];
    $allowed_airports = explode(',', $user_airport);
    $display="";
    $class='3';
    if($role_id!="1")
    {
        $display="display:none;";
        $class = '4';
    } 

    $airport_type=get_website_type($user_airport);

?>  
<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<style>
    .nav-tabs li{
        margin: 10px;
    }
    .nav-tabs li a{
        padding: 10px 30px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    .nav-tabs>li.active>a,.nav-tabs>li.active>a:hover{
        color: #555;
        cursor: default;
        background-color: #fff;
        border: 1px solid #ddd;
        border-bottom-color: transparent;
    }
    #amountData h3,#amountData h4, #amountData h6{
        font-weight: bold;
    }
    .cancel-refund{
        display: none;
    }
    .red-mark { background-color: #ffcccc; }
    .green-mark { background-color: #ccffcc; }
    .late-mark { background-color: darkred; color: #fff }
</style>
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
                     <!-- style="<?= $display; ?>;" -->
                    <div class="col-md-<?= $class?> col-<?= $class?>">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Booking Reference</label>
                            <input type="text" id="reference" name="reference" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-<?= $class?> col-<?= $class?>" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Surname</label>
                            <input type="text" id="surname" name="surname" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-<?= $class?> col-<?= $class?>">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Car Registration</label>
                            <input type="text" id="CarRegistration" name="CarRegistration" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-<?= $class?> col-<?= $class?>" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Email</label>
                            <input type="Email" id="Email" name="Email" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-3 col-3" style="<?= $display; ?>;">
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
                                <option value="4">Refund</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 col-3" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="airport">Airport</label>
                            <select class="form-select select2" id="airport" name="airport">
                                <option value="*">All</option> 
                                <?php $get_airports = get_airports();

                                    foreach ($get_airports as $code => $name) {
                                        if (in_array($code, $allowed_airports)): ?>

                                    <option value="<?= $code; ?>"><?= $name; ?></option>

                                <?php endif; } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 col-3" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="status">Select Website</label>
                            <select class="form-select select2" id="website" name="website"></select>
                        </div>
                    </div>

                    <div class="col-md-<?= $class?> col-<?= $class?>">
                        <div class="mb-1">
                            <label class="form-label" for="contactNumber">Contact Number</label>
                            <input type="text" id="contactNumber" name="contactNumber" class="form-control" />
                        </div>
                    </div>


                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Date From</label>
                            <input type="text" id="DateFrom" name="DateFrom" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">DateTo</label>
                            <input type="text" id="DateTo" name="DateTo" class="form-control" />
                        </div>
                    </div>                   

                    <div class="col-md-12 col-12">
                        <div class="mb-1">
                            <div class="custom-control custom-radio">
                                <input type="radio" value="departure_at" name="filter_date" class="custom-control-input" checked>
                                <label class="custom-control-label"  for="filter_date">Departure</label>
                                <input type="radio" value="return_at" name="filter_date" class="custom-control-input">
                                <label class="custom-control-label" for="filter_date">Return</label>

                            </div>                            
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <button type="submit" id="" onclick="search_data();" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <section id="ajax-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <ul class="nav nav-tabs mt-1">
                              <li class="active"><a data-toggle="tab" href="#depart" class="atab">Departures</a></li>
                              <li><a data-toggle="tab" href="#collect" class="atab">Completed</a></li>
                              <li><a data-toggle="tab" href="#noshow" class="atab">No Show</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="depart" class="tab-pane active">
                                    <h3 class="px-3">Departures</h3>
                                    <div class="card-datatable" style="margin: 10px;overflow-y:auto;">
                                        <table class="datatables-ajax table table-responsive" id="view-datatable">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>Website</th>
                                                    <th>Airport/Car Park</th>
                                                    <th>Customer Name</th>
                                                    <th>Depart Date</th>
                                                    <th>Return Date</th>
                                                    <th>Registration</th>
                                                    <th>Phone</th>
                                                    <th>Amount</th>
                                                    <th>Show</th>
                                                    <th>Action</th>   
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="collect" class="tab-pane">
                                    <h3 class="px-3">Collected Booking</h3>
                                    <div class="card-datatable" style="margin: 10px;overflow-y:auto;">
                                        <table class="datatables-ajax table table-responsive" id="view-datatable2">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>Website</th>
                                                    <th>Airport/Car Park</th>
                                                    <th>Customer Name</th>
                                                    <th>Depart Date</th>
                                                    <th>Return Date</th>
                                                    <th>Registration</th>
                                                    <th>Phone</th>
                                                    <th>Amount</th>
                                                    <th>CollectBy</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="noshow" class="tab-pane">
                                    <h3 class="px-3">No Show</h3>
                                    <div class="card-datatable" style="margin: 10px;overflow-y:auto;">
                                        <table class="datatables-ajax table table-responsive" id="view-datatable3">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>Website</th>
                                                    <th>Airport/Car Park</th>
                                                    <th>Customer Name</th>
                                                    <th>Depart Date</th>
                                                    <th>Return Date</th>
                                                    <th>Registration</th>
                                                    <th>Phone</th>
                                                    <th>Amount</th>
                                                    <th>Show</th>
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
            <button type="submit" class="btn btn-primary">Cancel Booking</button>
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

<div class="modal fade text-start" id="view_driver_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <form class="form_view_booking" id="form-view_booking" action="<?= base_url('bookings/view_booking'); ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
            <div class="modal-header">
              <h4 class="modal-title">  View Booking</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="view_driver_booking_details">                          
                <!-- <span id="view_booking_details"></span>-->
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
        <!-- </span>        -->
        </div>
      </div>
    </div>
</div>

<div class="modal fade text-start" id="add_note" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">  Add Note</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Note *</label>
                <textarea class="form-control" name="note_desc" id="note_desc" rows="10"></textarea>
            </div>
        </div>
         <div class="modal-footer">
             <button type="button" class="btn btn-gradient-primary" id="btnNote">Add</button>
         </div>
      </div>
    </div>
</div>

<div class="modal fade text-start" id="change_status" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">  Change Status</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Status *</label>
                <select class="form-select select2" id="cstatus" name="cstatus">
                    <option value="0">Pending</option>
                    <option value="1">Completed</option>                          
                    <option value="2">Cancelled</option>
                    <option value="3">No Show</option>
                    <option value="4">Refund</option>
                    <option value="5">Cancelled - Refund</option>
                </select>
            </div>
        </div>
         <div class="modal-footer">
             <button type="button" class="btn btn-gradient-primary" id="btnChangeStatus">Add</button>
         </div>
      </div>
    </div>
</div>

<div class="modal fade text-start" id="collect_mark" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"> Booking Mark Collect </h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-group mb-2">
                <label>Driver *</label>
                <select class="form-select select2" id="driver_id" name="driver_id"></select>
            </div>
            <div class="form-group charges">
                <label>Late Charges</label>
                <input type="text" name="late_charges" id="late_charges" class="form-control">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>
        </div>
         <div class="modal-footer">
             <button type="button" class="btn btn-gradient-primary" id="btnCollect">Add</button>
             <button type="button" class="btn btn-gradient-danger" id="btnDelete">Undo</button>
         </div>
      </div>
    </div>
</div>

<div class="modal fade text-start" id="clicksend" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="form-crud-title">Quick SMS</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">                            
                  <div class="col-md-6 col-6">
                    <div class="mb-1">
                      <label class="form-label" for="phone">Phone</label>
                      <input type="text" id="sms_phone" class="form-control" placeholder="Enter Mobile number" name="phone" />
                    </div>
                  </div>
                  <div class="col-md-6 col-6">
                    <div class="mb-1">
                      <label class="form-label" for="template">Template</label>
                      <select class="form-select select2" id="sms_template" name="template">
                          <option value="">Select Template</option>
                          <?php if ($templates) {
                            foreach ($templates as $key => $temp) 
                            {
                              $template_id = $temp->template_id;
                              $template_name = $temp->template_name;
                              echo "<option value='$template_id'>$template_name</option>";
                            }}
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
                <!-- <button type="reset" class="btn btn-secondary">Reset</button> -->
                <button type="submit" id="btnSMS" class="btn btn-primary">Continue</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">
    
    $('#DateFrom').flatpickr({
        dateFormat: "d-M-Y",
        defaultDate: ["<?= date("d-M-Y"); ?>"]
    });

    $('#DateTo').flatpickr({
        dateFormat: "d-M-Y",
        defaultDate: ["<?= date("d-M-Y"); ?>"]
    });
    $('.atab').on('click', function() {
        let id = $(this).attr('href');
        $('.nav-tabs').find('li').removeClass('active');
        $('.tab-content').find('div').removeClass('active');
        $(this).parent().addClass('active');
        $(id).addClass('active');
        $('#webInvoice').hide();
    });

    var table;
    var table2;
    var table3;
 
    table = $('#view-datatable').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/reportb'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = $("input[name='filter_date']:checked").val();
                d.DateFrom =  $("#DateFrom").val();
                d.DateTo = $("#DateTo").val();
                d.role_id ="<?= $role_id; ?>";
                d.airport=$("#airport").val(); 
                d.website=$("#website").val();   
            },
            complete: function(data) {
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [500, 1000, 5000, 10000],
            [500, 1000, 5000, 10000],
        ],
        buttons: [
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
        ]
    });
    // Collected
    table2 = $('#view-datatable2').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/report'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = 'collected';
                d.DateFrom =  $("#DateFrom").val();
                d.DateTo = $("#DateTo").val();
                d.role_id ="<?= $role_id; ?>";
                d.airport=$("#airport").val(); 
                d.website=$("#website").val();      
            },
            complete: function(data) {
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [500, 1000, 5000, 10000],
            [500, 1000, 5000, 10000],
        ],
        buttons: [
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
            }
        ]
    });
    // noShow
    table3 = $('#view-datatable3').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/report'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = 'noshow';
                d.DateFrom =  $("#DateFrom").val();
                d.DateTo = $("#DateTo").val();
                d.role_id ="<?= $role_id; ?>";
                d.airport=$("#airport").val(); 
                d.website=$("#website").val();   
            },
            complete: function(data) {
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [500, 1000, 5000, 10000],
            [500, 1000, 5000, 10000],
        ],
        buttons: [
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
            }
        ]
    });
    // Copy row data
    $('#view-datatable').on('click', '.copy-btn', function () {
        // Get the row data for the clicked button
        const rowData = table.row($(this).closest('tr')).data();
        var dataToCopy='';
        // Format the row data into a string
        // const rowText = rowData.join('\t'); // Tab-separated values
        if (rowData) {
          // Check if data is an array and copy
          dataToCopy = Array.isArray(rowData) ? rowData.slice(0, -1).join(", ") : [rowData];
          console.log("Copied Data:", dataToCopy[0]);
        }
        dataToCopy = dataToCopy[0][0]+', '+dataToCopy[0][1]+', '+dataToCopy[0][2]+', '+dataToCopy[0][3]+', '+dataToCopy[0][4]+', '+dataToCopy[0][5]+', '+dataToCopy[0][6]+', '+dataToCopy[0][7];
        // Exclude the last column (Action/Copy button)
        // const dataToCopy = rowData.slice(0, -1).join('\t');

        // Copy the row data to the clipboard
        const tempInput = $('<textarea>');
        $('body').append(tempInput);
        tempInput.val(dataToCopy).select();
        document.execCommand('copy');
        tempInput.remove();

        // Notify the user
        alert('Row data copied: ' + dataToCopy);
    });
    // Mark collected
    $('#ajax-datatable').on('click', '.collect-btn', function () {
        let booking_id = $(this).data('id');
        let airport = $(this).data('airport');
        let type = $(this).data('type');
        let late_charges = $(this).data('late');
        let driver_id = $(this).data('driver');
        let delBtn = $(this).data('delbtn');

        $('#btnCollect').attr('data-id',booking_id);

        $('.charges').hide();
        $('#btnDelete').hide();
        if (type == 'return_at' && late_charges) {
            $('.charges').show();
            $('#late_charges').val(late_charges);
        }
        if (delBtn) {
            $('#btnDelete').show();
            $('#btnDelete').attr('data-id',delBtn);
        }
        $('#collect_mark').modal('show');


        if (airport) 
        {
            $.ajax({
                url: "<?= url_to('bookings/get_drivers'); ?> ",
                type: 'GET',
                data:{
                    airport :airport,   
                    driver_id :driver_id,   
                },
                complete: function(data) {
                    // console.log('res: ',data.responseText);
                    $('#driver_id').html(data.responseText);
                }
            });
        }
    });

    $('#btnCollect').on('click',  function () 
    {
        let booking_id = $(this).data('id');
        let driver_id = $('#driver_id').val();
        let late_charges = $('#late_charges').val();
        let description = $('#description').val();

        if (booking_id && driver_id) 
        {
            $.ajax({
                url: "<?= url_to('bookings/mark_collected'); ?> ",
                type: 'GET',
                data:{
                    booking_id :booking_id,       
                    driver_id :driver_id,       
                    late_charges :late_charges,       
                    description :description,       
                    delete :'',       
                },
                complete: function(data) {
                    console.log('res: ',data);
                    toastr['success'](data.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    $('#collect_mark').modal('hide');
                    location.reload();
                }
            });

        }else{
            alert('Please Select Driver');
        }
    });

    $('#btnDelete').on('click',  function () {
        let booking_id = $(this).data('id');
        if (booking_id) 
        {
            $.ajax({
                url: "<?= url_to('bookings/mark_collected'); ?> ",
                type: 'GET',
                data:{
                    booking_id :booking_id,       
                    driver_id :'',       
                    late_charges :'',       
                    description :'',       
                    delete :1,       
                },
                complete: function(data) {
                    console.log('res: ',data);
                    toastr['success'](data.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    $('#collect_mark').modal('hide');
                    location.reload();
                }
            });

        }else{
            alert('Please Select Driver');
        }
    });
    // SMS clickSend
    $('#ajax-datatable').on('click', '.sms-btn', function () {
        let phone = $(this).data('phone');

        $('#sms_phone').val(phone);
        $('#message').val('');
        $('#sms_template').val('');
        $('#sms_template').trigger('change');
        $('#clicksend').modal('show');
    });
    $('#btnSMS').on('click',  function () {
        let phone = $('#sms_phone').val();
        let message = $('#message').val();
        
        if (phone && message) 
        {
            $.ajax({
                url: "<?= url_to('clicksend/sms_sent'); ?> ",
                type: 'GET',
                data:{
                    phone :phone,   
                    message :message,   
                },
                complete: function(data) {
                    // console.log('res', data);
                    toastr['success'](data.responseJSON.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    $('#clicksend').modal('hide');
                }
            });
        }
    });
    // select template
    $('#sms_template').on('change', function() 
    {
        $('#message').val('');
        if ($(this).val()) 
        {
            $.ajax({
                url: "<?= url_to('clicksend/get_template'); ?> ",
                type: 'GET',
                data: { template_id : $(this).val() },
                complete: function (data) {
                    // console.log('response',data.responseJSON.data);
                    $('#message').val(data.responseJSON.data.body);
                }
            });
        }
    });
    // Print slip for late charges
    $('#ajax-datatable').on('click', '.print-btn', function () {
        let booking_id = $(this).data('id');
        // console.log('booking_id',booking_id);
        $("<iframe>").hide().attr("src", "<?= base_url('bookings/print_collected?booking_id='); ?>"+booking_id).appendTo("body");
    });
    $('#view-datatable').on('click', '.webEdit', function() {
        let id = $(this).data('id');
       // console.log('id clicked'+id);
       $(this).removeClass("webEdit");
       $(this).addClass("webUpdate");
       $(this).text("Update");
       let html = '<select class="form-select select2" id="source-'+id+'" data-id="'+id+'" style="width: 100%">'+
                        '<?php $agents = get_agents();foreach ($agents as $code => $name) { ?>'+
                            '<option value="<?= $code; ?>"><?= $name; ?></option><?php } ?>'+
                    '</select>';
       $(this).before(html);
    });
    // Update Source
    $('#view-datatable').on('click', '.webUpdate', function() {
         let booking_id = $(this).data('id');
        let source = $('#source-'+booking_id).val();
        // console.log('new val: '+source);
        // console.log('id: '+booking_id);
        if (source && booking_id) 
        {
            $.ajax({
                url: "<?= url_to('bookings/update_source'); ?> ",
                type: 'GET',
                data:{
                    booking_id: booking_id,
                    source :source,       
                },
                complete: function(data) {
                    console.log('res: ',data);
                    toastr['success'](data.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    $('#source-'+booking_id).remove();
                    $('.webUpdate').attr('data-id', booking_id).remove();
                    table.draw();
                }
            });
        }
    });
    // Add Note Modal open
    $('#form-view_booking').on('click', '.addNote', function() {
        let id = $(this).data('id');
        let note_desc = $(this).data('note_desc');
        $('#add_note').modal('show');
        // if (reason == '') {
            note_desc = "<?php echo date("d M, Y h:i:s A")?>: ";
        // }
        $('#note_desc').val(note_desc);
        $('#btnNote').attr('data-id',id);
    });
    // Add note for booking
    $('#btnNote').on('click', function() {
        let booking_id = $(this).data('id');
        let note_desc = $('#note_desc').val();

        if (booking_id) 
        {
            $.ajax({
                url: "<?= url_to('bookings/update_note'); ?> ",
                type: 'GET',
                data:{
                    booking_id: booking_id,     
                    note_desc :note_desc,       
                },
                complete: function(data) {
                    console.log('res: ',data);
                    toastr['success'](data.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    $('#note_desc').val('');
                    $('#add_note').modal('hide');
                    $("#view_booking").modal('hide');

                }
            });
        }
    });
    // Websites list according to airport
    $('#airport').on('change', function() 
    {
        $.ajax({
            url: "<?= url_to('bookings/get_airport_websites'); ?> ",
            type: 'GET',
            data: { airport : $(this).val() },
            complete: function (data) {
                // console.log('response',data.responseText);
                $('#website').html(data.responseText);
            }
        });
    });

    function search_data() 
    {
        table.draw();
        table2.draw();
        table3.draw();
    }

    function show_status(id, currentStatus) {
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
                $.ajax({
                    url: "<?= base_url('bookings/show_status'); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: id,
                        status:'',
                        show_status: currentStatus,
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
        console.log(form);
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
                    minDate: null,//today
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

    function updateParkingStatus(id)
        {  var form = document.getElementById("form-complete_booking");    
           var status=$("#status").val();
           var price=$("#price").val();
           if (form) 
                {
                    var status = form.elements["status"].value;
                    var price = form.elements["price"].value;
                    var receipt_number = form.elements["receipt_number"].value;
                    var booking_type = form.elements["booking_type"].value;

                }
            $.ajax({
              url: "<?= base_url('bookings/update_status'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+id+"&status="+status+"&price="+price+"&receipt_number="+receipt_number+"&booking_type="+booking_type,
              beforeSend: function() {
               
              },
              success: function (res) {
                
                if(res.status){
                    toastr['success'](res.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                    hideModal("complete_booking");
                    table.draw();
                  }else{           
                      toastr['error'](res.message, 'Error!', {
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


    $("#form-cancel_booking").submit(function(e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function() {
               
        },
        success: function(res) {
          if(res.status){
            toastr['success'](res.message, 'Success!', {
              closeButton: true,
              tapToDismiss: true,
              progressBar: true
            });
            hideModal("cancel_booking");
            table.draw();
          }else{           
              toastr['error'](res.message, 'Error!', {
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
    });

    $("#form-make_refund").submit(function(e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function() {
               
        },
        success: function(res) {
          if(res.status){
            toastr['success'](res.message, 'Success!', {
              closeButton: true,
              tapToDismiss: true,
              progressBar: true
            });
            hideModal("make_refund");
            table.draw();
          }else{           
              toastr['error'](res.message, 'Error!', {
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
    });


    $("#form-edit_booking").submit(function(e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function() {
               
        },
        success: function(res) {
          if(res.status){
            toastr['success'](res.message, 'Success!', {
              closeButton: true,
              tapToDismiss: true,
              progressBar: true
            });
            hideModal("edit_booking");
            table.draw();
          }else{           
              toastr['error'](res.message, 'Error!', {
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
    });


    function move_booking_save(id,price,product_id)
    {
        $.ajax({
              url: "<?= base_url('bookings/update_move_booking'); ?>",
              type: 'GET',
              dataType: 'json',
              data:"id="+id+"&price="+price+"&product_id="+product_id,
              beforeSend: function() {
               
              },
              success: function (res) {
                
                if(res.status){
                    toastr['success'](res.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                    hideModal("move_booking");
                    table.draw();
                  }else{           
                      toastr['error'](res.message, 'Error!', {
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

</script>
<?= $this->endSection(); ?>