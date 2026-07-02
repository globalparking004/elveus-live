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
                <h1 class="mt-4">Create Booking Step 2:</h1>
                <br><br><br>
                <div class="row">
                    <div class="col-md-6">
                        <?php foreach($data as $d){

// print_r($d);
                        ?>
                        <div class="col-md-12" data-price="88.99" data-distance="" data-score="93">
                            <div class="row">
                                <div class="col-sm-8">
                                    <h3><?= $d->name;?> </h3>
                                </div>
                                <div class="col-sm-2">
                                    <h3 class="green">&pound;<?= $d->score_price;?></h3>
                                </div>
                                <div class="col-sm-2">
                                    <?php  $url=url_to('create_booking3');?>
                                    <a href="<?=$url?>?request=e1f0dee6d2aa8be9116308aa4063c207&quote=3419533&agent=14"><button id="singlebutton" name="carpark_details" class="disableButton btn btn-primary w-100 mr-2 mr-sm-0">Book Now</button></a>
                                </div>
                            </div>
                        </div>
                        <br>
                        <?php    }?>
                    </div>
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