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

            <div class="container">
                <h1 class="mt-4">Create Booking Step 3:</h1>
                <br><br>
                <form action="<?= url_to('create_booking4');?>" method="post" class="booking-form" id="bookingformv2">
                <?= csrf_field() ?> <!-- Include the CSRF token -->

                    <div id="accordion">

                        <div class="card mb-3 box-shadow-dark">
                            <div class="card-header" id="headingOne">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-0">Enter new price If Required (Currently set to £86.99). Leave blank if price is correct.</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="mb-0">Enter Booking reference if required, leave blank to create new reference.</h4>
                                    </div>
                                </div>
                            </div>
                            <div id="collapseOne" class="" aria-labelledby="headingOne">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6 px-1">
                                            <input type="text" class="form-control" name="new_price" value="">
                                        </div>
                                        <div class="form-group col-md-6 px-1">
                                            <input type="text" class="form-control" name="new_reference" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3 box-shadow-dark">
                            <div class="card-header" id="headingOne">
                                <h4 class="mb-0">
                                    <!--                    <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">-->
                                    Car Details <!--                       <i class="fas fa-caret-down"></i>-->
                                    <!--                    </button>-->


                                </h4>


                            </div>

                            <div id="collapseOne" class="" aria-labelledby="headingOne">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6 px-1">
                                            <input type="text" class="form-control" id="carReg" name="carReg" value="" placeholder="Car Registration" required>
                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" class="form-control" id="carMake" name="carMake" value="" placeholder="Car Manufacturer" required>
                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" class="form-control" id="carModel" name="carModel" value="" placeholder="Car Model" required>
                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" class="form-control" id="carColour" name="carColour" value="" placeholder="Car Colour" required>
                                        </div>
                                    </div>

                                    <script>
                                        var currentReg = $('#carReg').val();
                                        $('#carReg').on('change keyup paste mouseup', function() {
                                            if (this.value != currentReg) {
                                                $.ajax({
                                                    type: 'POST',
                                                    url: '../checkVehicle.php',
                                                    data: {
                                                        reg: this.value
                                                    },
                                                    dataType: "json",
                                                    success: function(response) {
                                                        if (response.status == "success") {
                                                            $("#carColour").val(response.colour);
                                                            $("#carMake").val(response.make);
                                                            $("#carModel").val(response.model);
                                                        } else {
                                                            alert("No data found for this vehicle");
                                                        }
                                                    }
                                                });
                                                currentReg = this.value;
                                            }
                                        });
                                    </script>

                                </div>
                            </div>
                        </div>

                        <div class="card mb-3 box-shadow-dark">
                            <div class="card-header" id="headingTwo">
                                <h4 class="mb-0">Flight Details</h4>
                            </div>
                            <div id="collapseTwo" class="" aria-labelledby="headingTwo">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" name="required[OutTerminal]" class="form-control" maxlength="2" id="out-terminal" placeholder="Departure Terminal" />


                                        </div>

                                        <div class="form-group col-md-6 px-1">
                                            <input type="text" name="required[RetTerminal]" class="form-control" maxlength="2" id="return-terminal" placeholder="Return Terminal" />


                                        </div>


                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" name="required[OutFltNo]" class="form-control" maxlength="10" id="departure-flight-number" placeholder="Departure Flight Number" />

                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" name="required[InFltNo]" class="form-control" maxlength="10" id="inbound-flight-number" placeholder="Return Flight Number" />

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3 box-shadow-dark">
                            <div class="card-header" id="headingTwo">
                                <h4 class="mb-0">Customer Details - Only enter email address if known otherwise use the randomly generated address below</h4>
                            </div>
                            <div id="collapseTwo" class="" aria-labelledby="headingTwo">
                                <div class="card-body">
                                    <div class="row">


                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" class="form-control required" id="firstName" name="firstName" value="" placeholder="First Name" required>
                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" class="form-control" id="surname" name="surname" value="" placeholder="Surname" required>
                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="email" class="form-control" id="email" name="email" value="d41d8cd98f00b204e9800998ecf8427e@20230905052810-opitech.co" placeholder="Email" required>
                                        </div>

                                        <div class="form-group col-md-6 px-1">

                                            <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="" placeholder="Contact Number" required>
                                        </div>


                                        <input type="hidden" name="title" value="Mr/s">
                                        <input type="hidden" name="billing_address_line_1" data-stripe="address_line1" value="XXXX">
                                        <input type="hidden" name="billing_address_post_code" data-stripe="address_zip" value="XX00XX">
                                        <input type="hidden" data-stripe="address_country" value="GB">

                                    </div>

                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="request_token" value="eaf81c89a5ad39a71097fcc69c2797c9" />
                        <input type="hidden" name="parkingQuoteId" value="3419520" />
                        <input type="hidden" name="opitechAgentId" value="14" />

                        <div class="row mt-2 mb-3">


                            <div class="col-md-12 px-0 mt-2">
                                <button type="submit" id="submitBooking" name="completebooking" value="no-payment" class="btn btn-primary w-100" style="margin-bottom:100px;"><i class="fas fa-lock"></i> Complete Booking</button>

                            </div>
                        </div>


                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<!-- END: Content-->



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
            var selectedDateStr = $("#selectedDate").val();
            var selectedDate = new Date(selectedDateStr);

            if (!isNaN(selectedDate.getTime())) {
                var newDate = addDays(selectedDate, 7);
                var newDateStr = newDate.toISOString().split('T')[0]; // Format as yyyy-mm-dd
                $("#changedDate").val(newDateStr);
            } else {
                $("#changedDate").val(""); // Clear the changed date if the selected date is invalid
            }
        }

        // Detect changes in the input field
        $("#selectedDate").on("change", updateDate);

        // Initialize the changed date based on the initial date (if any)
        updateDate();
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
                $("#form-crud-title").html("Modify Operators");
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
</script>
<?= $this->endSection(); ?>