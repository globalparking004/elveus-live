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
                     <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Booking Reference</label>
                            <input type="text" id="reference" name="reference" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Surname</label>
                            <input type="text" id="surname" name="surname" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Car Registration</label>
                            <input type="text" id="CarRegistration" name="CarRegistration" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="contactNumber">Contact Number</label>
                            <input type="text" id="contactNumber" name="contactNumber" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="airport">Airport</label>
                            <select class="form-select select2" id="airport" name="airport">
                                <option value="*">All</option> 
                                <?php $get_airports = get_airports();

                                    foreach ($get_airports as $code => $name) { 
                                        if (in_array($code, $allowed_airports)):?>

                                    <option value="<?= $code; ?>"><?= $name; ?></option>

                                <?php endif; } ?>
                            </select>
                        </div>
                    </div>



                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="status">Select Website</label>
                            <select class="form-select select2" id="website" name="website"></select>
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
                              <li><a data-toggle="tab" href="#return" class="atab">Returns</a></li>
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
                                <div id="return" class="tab-pane">
                                    <h3 class="px-3">Returns</h3>
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
                                        <table class="datatables-ajax table table-responsive" id="view-datatable4">
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
    var table4;
 
    table = $('#view-datatable').DataTable({
        dom: '<l>frtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/report'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = 'departure_at';
                d.reference = $("#reference").val();
                d.surname =  $("#surname").val();
                d.CarRegistration = $("#CarRegistration").val();
                d.contactNumber=$("#contactNumber").val();
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
        ]
    });
    // Returns
    table2 = $('#view-datatable2').DataTable({
        dom: '<l>frtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/report'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = 'return_at';
                d.reference = $("#reference").val();
                d.surname =  $("#surname").val();
                d.CarRegistration = $("#CarRegistration").val();
                d.contactNumber=$("#contactNumber").val();
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
        ]
    });
    // Collected
    table3 = $('#view-datatable3').DataTable({
        dom: '<l>frtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/report'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = 'collected';
                d.reference = $("#reference").val();
                d.surname =  $("#surname").val();
                d.CarRegistration = $("#CarRegistration").val();
                d.contactNumber=$("#contactNumber").val();
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
        ]
    });
    // noShow
    table4 = $('#view-datatable4').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('bookings/driver/report'); ?> ",
            type: 'GET',
            data: function(d) {
                d.filter_date = 'noshow';
                d.reference = $("#reference").val();
                d.surname =  $("#surname").val();
                d.CarRegistration = $("#CarRegistration").val();
                d.contactNumber=$("#contactNumber").val();
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
            alert('Driver or Booking is missing');
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
                    url: "<?= base_url('products/delete_record'); ?>",
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

    function search_data() {

        table.draw();
        table2.draw();
        table3.draw();
        table4.draw();
    }


    function print_card(id)
    {  
         $("<iframe>").hide().attr("src", "<?= base_url('bookings/print_card?id='); ?>"+encodeURIComponent(id)).appendTo("body");
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

</script>
<?= $this->endSection(); ?>