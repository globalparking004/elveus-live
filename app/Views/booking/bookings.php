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

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Search Bookings</h4>
                        </div>
                        <div class="panel-body">



                            <form method="get" class="form-inline">
                                <div class="row margin-bottom-15">
                                    <!-- First Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Booking Reference</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="booking_reference" value="" class="form-control input-100-pc" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="surname">Surname</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="surname" value="" class="form-control input-100-pc" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Repeat the same structure for the remaining form fields -->

                                <!-- Third Row -->
                                <div class="row margin-bottom-15">
                                    <!-- First Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="car-registration">Car Registration</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="car_registration" class="form-control input-100-pc" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="email">Email</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="email" name="email" value="" class="form-control input-100-pc" />
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- Third Row -->
                                <div class="row margin-bottom-15">
                                    <!-- First Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Date From</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" name="date[from]" value="2023-09-05" class="form-control input-100-pc" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Date To</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" name="date[to]" value="2023-09-05" class="form-control input-100-pc" />
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Third Row -->
                                <div class="row margin-bottom-15">
                                    <!-- First Column -->
                                    <div class="form-group-sm col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Status</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="status" class="form-control input-100-pc">
                                                    <option value="">-- select status --</option>
                                                    <option value="1">Pending</option>
                                                    <option value="2">Completed</option>
                                                    <option value="3">Cancelled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Continue the pattern for the remaining form fields -->

                                <!-- Add the submit button in a separate row -->
                                <div class="row margin-top-10">
                                    <div class="col-md-12">
                                        <input type="submit" name="search-bookings" value="Search" class="btn btn-sm btn-primary btn-block">
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <strong>Found: 11</strong>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr />
                    <table class="table table-stripe table-hover">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th class="text-center">Reference</th>
                                <th class="text-center">Amount</th>
                                <th>Customer Name</th>
                                <th>Airport/Car Park</th>
                                <th class="text-center">Registration</th>
                                <th>Booked At</th>
                                <th class="text-center">Depart Date</th>
                                <th class="text-center">Return Date</th>
                                <th class="text-center">Status</th>
                                <th>Source</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    Global - Leeds </td>
                                <td class="text-center"><b>P4U-524120 </b></td>
                                <td class="text-center">

                                    78.99
                                </td>
                                <td>Mr. Jason Lloyd</td>
                                <td>
                                    Leeds Bradford<br />
                                    Leeds Airport Parking Services Meet and Greet </td>
                                <td class="text-center">SA67OOG</td>
                                <td>05/09/2023 06:53</td>
                                <td class="text-center">
                                    27/08/2023 </td>
                                <td class="text-center">
                                    04/09/2023 </td>
                                <td class="text-center"><span class="label label-success"><b>COMPLETED</b></span></td>
                                <td>Dashboard Booking</td>
                                <td>
                                    <a href="view.php?id=60449" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
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