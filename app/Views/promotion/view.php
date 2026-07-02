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
                            <h2 class="content-header-title float-start mb-0">
                                <?= $page_title; ?>
                            </h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <?php for ($i = 0; $i < sizeof($breadcrumb); $i++) {
                                        extract($breadcrumb[$i]);
                                        ?>
                                        <?php if ($link) { ?>
                                            <li class="breadcrumb-item <?= $status; ?>"><a href="<?= $href; ?>"><?= $title; ?></a>
                                            </li>
                                        <?php } else { ?>
                                            <li class="breadcrumb-item <?= $status; ?>"><?= $title; ?></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ol>
                            </div>
                        <?php } else { ?>
                            <h2 class="">
                                <?= $page_title; ?>
                            </h2>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="content-body">


            <!-- Step 1 -->
            <div class="step" id="step-1">
                <form class="form-crud" action="<?= base_url('promotion/save'); ?>">
                <?= csrf_field() ?>


                    <section id="ajax-datatable">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-datatable" style="margin: 10px;">
                                        <div class="container">
                                            <div class="row">
                                                <h1 class="mt-4">Add Promotion Code</h1>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Code</label>
                                                        <input type="text" id="code" class="form-control" placeholder=""
                                                            name="code" required />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Type:</label>
                                                        <select class="form-select select2" id="" name="type" required>
                                                        <option value="">Select Value</option>

                                                            <option value="value">Value</option>
                                                            <option value="Percentage">Percentage</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Amount</label>
                                                        <input type="number" id="" class="form-control" placeholder="" required
                                                            name="amount" />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Promotion
                                                            Name:</label>
                                                        <input type="text" id="" class="form-control" placeholder=""required
                                                            name="promotion_name" />
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Agent
                                                            Name:</label>
                                                        <input type="text" id="" class="form-control" placeholder=""
                                                            name="agent" />
                                                    </div>
                                                </div> -->
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="agent">Agent Name</label>
                                                        <select class="form-select select2" id="" name="agent" required>
                                                        <option value="">Select Agent Name</option>
                                                          <?php foreach($agents as $r){
                                                            $name=$r->agent;
                                                            echo "<option value='$name'>$name</option>";
                                                          }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="status">Select Website</label>
                                                        <select class="form-select select2" id="" name="website" required>
                                                        <option value="">Select Value</option>
                                                          <option value="All">All</option>

                                                          <?php foreach($websites as $r){
                                                            $name=$r->domain;
                                                            echo "<option value='$name'>$name</option>";
                                                          }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Valid From:</label>
                                                        <input type="text" id="dfrom" class="form-control"
                                                            placeholder="" name="valid_from" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="description">Valid To:</label>
                                                        <input type="text" id="dto" class="form-control" placeholder=""
                                                            name="valid_to" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <br>
                                                        <button type="submit" id="btnsubmit" 
                                                            class="btn btn-primary">Save</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
            </div>

            </form>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">
    $(document).ready(function () {
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
                var month = newDate.getMonth() + 1;
                var day = newDate.getDate();
                var year = newDate.getFullYear();

                // Add leading zero for single digit months and days
                if (month < 10) {
                    month = "0" + month;
                }

                if (day < 10) {
                    day = "0" + day;
                }

                var newDateStr = month + "/" + day + "/" + year; // Format as m/d/y
                $("#dto").val(newDateStr);
            } else {
                $("#dto").val(""); // Clear the changed date if the selected date is invalid
            }
        }

        // Detect changes in the input field
        $("#dfrom").on("change", updateDate);

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
            data: function (d) {

            },
            complete: function (data) {
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

        submitHandler: function (form) {
            var formData = $(form).serialize();
            $.ajax({
                url: $(form).attr("action"),
                type: 'POST',
                dataType: 'json',
                data: formData,
                beforeSend: function () {
                    $("#btnsubmit").attr("disabled", true);
                },
                success: function (data) {
                    if (data.msg) {
                        toastr['success'](data.msg, 'Success!', {
                            closeButton: true,
                            tapToDismiss: true,
                            progressBar: true
                        });
                        table.draw();
                        hideModal("add-xlarge");
                        setTimeout(function () {
                            // window.location.href = "<?= base_url('promotion'); ?>";
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
                error: function (xhr) {
                    $("#btnsubmit").attr("disabled", false);
                },
                complete: function () {
                    $("#btnsubmit").attr("disabled", false);
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



    $("#add-xlarge").on("hidden.bs.modal", function () {
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

        // Send an AJAX request
        $.ajax({
            type: "GET",
            url: "<?= url_to('create_booking2'); ?>", // Replace with the actual URL to handle the request
            data: data,
            success: function (response) {
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
            error: function (error) {
                // Handle errors here
                console.error(error);
            }
        });
    }
    $('.prev-step22').click(function () {
        showStep(1);
    });

    function showStep(step) {
        $('.step').hide();
        $('#step-' + step).show();
    }


    $('#dfrom').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["09/06/2023"]
    });

    $('#dto').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["09/06/2023"]
    });


    $(document).ready(function () {
        $(".next-step").click(function () {
            var activeStep = $(this).attr("data-step");
            $(".collapse").removeClass("show");
            $("#" + activeStep).collapse("show");
        });

        $(".prev-step").click(function () {
            var activeStep = $(this).attr("data-step");
            $(".collapse").removeClass("show");
            $("#" + activeStep).collapse("show");
        });

        $("#form-crud").submit(function () {
            var formData = $("#multi-step-form").serialize();

            $.ajax({
                type: 'POST',
                url: "<?= url_to('create_booking3'); ?>",
                data: formData,
                success: function (response) {
                    $("#result").html("Form submitted successfully.");
                },
                error: function (error) {
                    $("#result").html("An error occurred: " + error.statusText);
                }
            });

            return false;
        });
    });
</script>
<?= $this->endSection(); ?>