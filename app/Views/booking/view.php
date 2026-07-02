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

            <div class="row">
                <div class="col-md-6 col-12">
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <h3>Choose CSV file</h3>
                            <!-- <form class="" action="<?= base_url('bookings/upload'); ?>" method="POST">
                            <?= csrf_field() ?>

                                <label for="myfile">Select a file:</label>
                                    <input type="file" id="myfile" name="fileToUpload" accept=".csv, .xlsx">
                                <input type="submit" id="uploadsubmit" class="btn btn-primary">
                            </form> -->
                                <form action="<?= base_url('bookings/upload') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>

                                <input type="file" name="fileToUpload" id="fileToUpload">
                                <input type="submit" value="Upload File" name="submit" class="btn btn-primary">
                                </form>



                    </div>
                </div>
            </div>

            <!-- Step 1 -->
            <div class="step" id="step-1">
                <form class="form-crud" action="<?= base_url('bookings/save'); ?>">

                    <section id="ajax-datatable">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-datatable" style="margin: 10px;">


                                        <div class="container">
                                            <div class="row">
                                                <h1 class="mt-4">Add Booking</h1>

                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Airport</label>
                                                        <select class="form-select select2" id="airport" name="location_code">
                                                            <option value="">Select Airport</option>
                                                            <?php $get_airports = get_airports();

                                                            foreach ($get_airports as $code => $name) { ?>

                                                                <option value="<?= $code; ?>"><?= $name; ?></option>

                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Agent</label>
                                                        <select class="form-select select2" id="agent" name="opitech_agent"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Parking From:</label>
                                                        <input type="text" id="dfrom" class="form-control" placeholder="" name="arrival_date" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Timing</label>
                                                        <select class="form-select select2" id="arrival_time" name="arrival_time">
                                                            <?php $get_shift_time = get_booking_shift_time();

                                                            foreach ($get_shift_time as $code => $name) { ?>

                                                                <option value="<?= $code; ?>"><?= $name; ?></option>

                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Collecting Car:</label>
                                                        <input type="text" id="dto" class="form-control" placeholder="" name="departure_date" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Timing</label>
                                                        <select class="form-select select2" id="departure_time" name="departure_time">
                                                            <?php $get_shift_time = get_booking_shift_time();

                                                            foreach ($get_shift_time as $code => $name) { ?>

                                                                <option value="<?= $code; ?>"><?= $name; ?></option>

                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <button type="button" id="btnsubmit" onclick="submitForm()" class="btn btn-primary">Continue</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
            </div>

            <!-- Step 2 -->
            <div class="step" id="step-2" style="display: none;">
                <section id="ajax-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable" style="margin: 10px;">
                                    <table class="datatables-ajax table table-responsive" id="view-">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Price</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody id="step2data"></tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary prev-step22">Previous</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="modal fade text-start" id="add-xlarge" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" id="id" value="" />
                        <input type="hidden" name="cal_price" id="cal_price" value="" />
                        <input type="hidden" name="operatorid" id="operatorid" value="" />

                        <div class="modal-header">
                            <h4 class="modal-title" id="form-crud-title">Add Booking</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Enter new price If Required (Currently set to £79.99). Leave blank if price is correct.</label>
                                        <input type="text" class="form-control" name="new_price" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Enter Booking reference if required, leave blank to create new reference.</label>
                                        <input type="text" class="form-control" name="new_reference" value="">
                                    </div>
                                </div>
                                <h5>Car Details</h5>

                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Car Registration</label>
                                        <input type="text" class="form-control" id="carReg" name="carReg" value="" placeholder="Car Registration" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Car Manufacturer</label>
                                        <input type="text" class="form-control" id="carMake" name="carMake" value="" placeholder="Car Manufacturer" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Car Model</label>
                                        <input type="text" class="form-control" id="carModel" name="carModel" value="" placeholder="Car Model" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Car Colour</label>
                                        <input type="text" class="form-control" id="carColour" name="carColour" value="" placeholder="Car Colour" required>
                                    </div>
                                </div>
                                <h5>Flight Details</h5>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Departure Terminal</label>
                                        <input type="text" name="required[OutTerminal]" class="form-control" maxlength="2" id="out-terminal" placeholder="Departure Terminal" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Return Terminal</label>
                                        <input type="text" name="required[RetTerminal]" class="form-control" maxlength="2" id="return-terminal" placeholder="Return Terminal" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Departure Flight Number</label>
                                        <input type="text" name="required[OutFltNo]" class="form-control" maxlength="10" id="departure-flight-number" placeholder="Departure Flight Number" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">"Return Flight Number</label>
                                        <input type="text" name="required[InFltNo]" class="form-control" maxlength="10" id="inbound-flight-number" placeholder="Return Flight Number" />
                                    </div>
                                </div>

                                <h5>Customer Details - Only enter email address if known otherwise use the randomly generated address below</h5>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">First Name</label>
                                        <input type="text" class="form-control required" id="firstName" name="firstName" value="" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Surname</label>
                                        <input type="text" class="form-control" id="surname" name="surname" value="" placeholder="Surname" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="" placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Contact Number</label>
                                        <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="" placeholder="Contact Number">
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="description">Passenger</label>
                                        <input type="text" min="0" class="form-control" id="passenger" name="passenger" value="" placeholder="No Of Passenger">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="btnsubmit" class="btn btn-primary">Continue</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>



            <?= $this->endSection(); ?>
            <?= $this->section("javascript"); ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    // Function to add 7 days to a given date
                    function addDays(date, days) {
                        var result = new Date(date);
                        result.setDate(result.getDate() + days);
                        return result;
                    }

                    // Function to update the changed date based on the selected date
                    function updateDate() {
                        var selectedDateStr = $("#dfrom").val();
                        var selectedDate = new Date(selectedDateStr);

                        if (!isNaN(selectedDate.getTime())) {
                            var newDate = addDays(selectedDate, 7);
                            // var month = newDate.getMonth() + 1;
                            var month = newDate.getMonth();
                            var day = newDate.getDate();
                            var year = newDate.getFullYear();
                            if (day < 10) {
                                day = "0" + day;
                            }
                            const monthNames = [
                              "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                              "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                            ];
                            month = monthNames[month];
                            var newDateStr = day + "-" + month + "-" + year;
                            $("#dto").val(newDateStr);
                        } else {
                            $("#dto").val(""); // Clear the changed date if the selected date is invalid
                        }
                    }

                    // Detect changes in the input field
                    $("#dfrom").on("change", updateDate);

                    // Initialize the changed date based on the initial date (if any)
                    updateDate();
                    $('#agent').change( function() {
                        let agent = $(this).val();
                        $('input[name="new_reference"]').val('');
                        if (agent == 'Cash Booking') {
                            $('input[name="new_reference"]').val('CASH-');
                        }
                    })

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
                            $('#agent').html(data.responseText);
                        }
                    });
                });


                $('.select2').select2();
                var table;
                table = $('#view-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    select: true,
                    ajax: {
                        url: "<?= url_to('operators/get'); ?> ",
                        type: 'GET',
                        data: function(d) {

                        },
                        complete: function(data) {
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
                                if (data.msg) {
                                    toastr['success'](data.msg, 'Success!', {
                                        closeButton: true,
                                        tapToDismiss: true,
                                        progressBar: true
                                    });
                                    table.draw();
                                    hideModal("add-xlarge");
                                    setTimeout(function() {
                                        window.location.href = "<?= base_url('bookings/add'); ?>";
                                    }, 2000);


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

                var validator = $(".form_upload").validate({

                submitHandler: function(form) {
                    var formData = $(form).serialize();
                    $.ajax({
                        url: $(form).attr("action"),
                        type: 'GET',
                        dataType: 'json',
                        data: formData,
                        beforeSend: function() {
                            $("#uploadsubmit").attr("disabled", true);
                        },
                        success: function(data) {
                            if (data.msg) {
                                toastr['success'](data.msg, 'Success!', {
                                    closeButton: true,
                                    tapToDismiss: true,
                                    progressBar: true
                                });
                                table.draw();
                                hideModal("add-xlarge");
                                setTimeout(function() {
                                    window.location.href = "<?= base_url('bookings/upload'); ?>";
                                }, 2000);


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
                            $("#uploadsubmit").attr("disabled", false);
                        },
                        complete: function() {
                            $("#uploadsubmit").attr("disabled", false);
                        }
                    });
                    return false;
                }
                });

               
                function edit_data(id, price, operatorid) {
                    $("#id").val(id);
                    $("#cal_price").val(price);
                    $("#operatorid").val(operatorid);

                    $("#form-crud").attr("action", "<?= base_url('operators/update'); ?>");
                    showModal("add-xlarge");
                }



                $("#add-xlarge").on("hidden.bs.modal", function() {
                    $("#form-crud")[0].reset();
                    $("#id").val('');
                    $("#form-crud-title").html("Add Operators");
                    $("#form-crud").attr("action", "<?= base_url('operators/save'); ?>");
                });



                function submitForm() {
                    // Get values by ID
                    var airport = document.getElementById("airport").value;
                    var selectedDate = document.getElementById("dfrom").value;
                    var changedDate = document.getElementById("dto").value;
                    var agent = document.getElementById("agent").value;
                    var arrivalTime = document.getElementById("arrival_time").value;
                    var departureTime = document.getElementById("departure_time").value;

                    // Create an object with the data
                    var data = {
                        airport: airport,
                        selectedDate: selectedDate,
                        changedDate: changedDate,
                        agent: agent,
                        arrivalTime: arrivalTime,
                        departureTime: departureTime
                    };
                    if (agent && agent !='*' && airport) 
                    {
                        // Send an AJAX request
                        $.ajax({
                            type: "GET",
                            url: "<?= url_to('create_booking2'); ?>", // Replace with the actual URL to handle the request
                            data: data,
                            success: function(response) {
                                var data = JSON.parse(response);

                                if (data.msg) {
                                    toastr['error'](data.msg, 'Not Available!', {
                                        closeButton: true,
                                        tapToDismiss: true,
                                        progressBar: true
                                    });

                                } else {

                                    console.log(response);
                                    $("#step2data").html(data.msssg);
                                    showStep(2);

                                }
                                // Handle the AJAX response here

                            },
                            error: function(error) {
                                // Handle errors here
                                console.error(error);
                            }
                        });
                    }else{
                        alert('Airport and Agent must be selected')
                    }
                }
                $('.prev-step22').click(function() {
                    showStep(1);
                });

                function showStep(step) {
                    $('.step').hide();
                    $('#step-' + step).show();
                }


                $('#dfrom').flatpickr({
                    dateFormat: "d-M-Y",
                    defaultDate: ["<?= date("d-M-Y"); ?>"]
                });

                $('#dto').flatpickr({
                    dateFormat: "d-M-Y",
                    defaultDate: ["<?= date("d-M-Y"); ?>"]
                });


                $(document).ready(function() {
                    $(".next-step").click(function() {
                        var activeStep = $(this).attr("data-step");
                        $(".collapse").removeClass("show");
                        $("#" + activeStep).collapse("show");
                    });

                    $(".prev-step").click(function() {
                        var activeStep = $(this).attr("data-step");
                        $(".collapse").removeClass("show");
                        $("#" + activeStep).collapse("show");
                    });

                    $("#form-crud").submit(function() {
                        var formData = $("#multi-step-form").serialize();

                        $.ajax({
                            type: 'POST',
                            url: "<?= url_to('create_booking3'); ?>",
                            data: formData,
                            success: function(response) {
                                $("#result").html("Form submitted successfully.");
                            },
                            error: function(error) {
                                $("#result").html("An error occurred: " + error.statusText);
                            }
                        });

                        return false;
                    });
                });
            </script>
            <?= $this->endSection(); ?>