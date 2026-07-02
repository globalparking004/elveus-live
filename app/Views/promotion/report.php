<?php

ini_set('memory_limit', '-1');

$AUTH = session()->get('AUTH');
$role_id = $AUTH['role_id'];
$role_name=$AUTH['role_name'];
$display = "";
if ($role_id != "1" && $role_name !='CSR') {
    $display = "display:none;";
}
?>
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
                                            <li class="breadcrumb-item <?= $status; ?>"><a href="<?= $href; ?>">
                                                    <?= $title; ?>
                                                </a></li>
                                        <?php } else { ?>
                                            <li class="breadcrumb-item <?= $status; ?>">
                                                <?= $title; ?>
                                            </li>
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

            <div class="card">
                <h5 class="card-header">Search Filter </h5>
                <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">

                    <div class="col-md-3 col-3" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Code</label>
                            <input type="text" id="code" name="code" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-3 col-3" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Promotion Name</label>
                            <input type="text" id="promotional_name" name="promotional_name" class="form-control" />
                        </div>
                    </div>




                    <div class="col-md-3 col-3" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label">Agent</label>
                                <select class="form-select select2" id="agent" name="agent" required>
                                    <option value="">Select Agent Name</option>
                                        <?php foreach($agents as $r){
                                        $name=$r->agent;
                                        echo "<option value='$name'>$name</option>";
                                        }?>
                                </select>
                        </div>
                    </div>

                        <div class="col-md-3 col-3">
                            <div class="mb-1">
                                <label class="form-label">Website</label>
                                <select class="form-select select2" id="website" name="website">
                                    <option value="">Select Website</option>
                                    <option value="All">All</option>
                                    <?php foreach ($websites as $website) {
                                        $name = $website->domain;
                                        echo "<option value='$name'>$name</option>";
                                    }?>
                                </select>
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
                            <?php if (strval($role_id) > 1 && $role_name !='CSR') { ?>
                                <button type="submit" id="" onclick="print_data();" class="btn btn-info">Print
                                    Bookings</button>
                                <button type="submit" id="" onclick="print_dards();" class="btn btn-danger">Print
                                    Cards</button>
                            <?php } ?>
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
                                            <th>Code</th>
                                            <th>Promotion Name</th>
                                            <th>Agent</th>
                                            <th>Amount</th>
                                            <th>Website</th>
                                            <th>Usage Of Code</th>
                                            <th>Valid From</th>
                                            <th>Valid To</th>
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

</div>
</div>


<!-- END: Content-->



<div class="modal fade text-start" id="add-xlarge" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form class="form-crud" id="form-crud" action="<?= base_url(''); ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" value="" />
                <div class="modal-header">
                    <h4 class="modal-title" id="form-crud-title">Add User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="first_name">CODE</label>
                                <input type="text" id="CODE" class="form-control" placeholder="First Name"
                                    name="CODE" />
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="last_name">PROMOTION NAME</label>
                                <input type="text" id="PROMOTIONNAME" class="form-control" placeholder="Last Name"
                                    name="PROMOTIONNAME" />
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="email">Agent</label>
                                <select class="form-select select2" id="AGENT" name="AGENT" required>
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
                                <label class="form-label" for="phone">Amount</label>
                                <input type="text" id="amount" class="form-control" placeholder="amount"
                                    name="amount" />
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="airport">Type:</label>
                                <select class="form-select select2" id="type" name="type">
                                    <option value=""></option>
                                        <option value="value">Value</option>
                                        <option value="Percentage">Percentage</option>
                                        
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="airport">Website</label>
                                <select class="form-select select2" id="website" name="website">
                                    <option value=""></option>
                                    <option value="All">All</option>

                                    <?php
                                    foreach ($websites as $website) {
                                        $name = $website->domain;

                                        echo "<option value='$name'>$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="phone">Valid From</label>
                                <input type="text" id="valid_from" class="form-control" placeholder="valid from"
                                    name="valid_from" />
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="phone">Valid To</label>
                                <input type="text" id="valid_to" class="form-control" placeholder="valid to"
                                    name="valid_to" />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" id="btnsubmit" class="btn btn-primary">Continue</button>
                </div>
            </form>
        </div>
    </div>
</div>






<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">

    $('#valid_from').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });

    $('#valid_to').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });

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
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('promotion/get'); ?> ",
            type: 'GET',
            data: function (d) {
                d.agent = $("#agent").val();
                d.code = $("#code").val();
                d.promotional_name = $("#promotional_name").val();
                d.website = $("#website").val();
                d.DateFrom = $("#DateFrom").val();
                d.DateTo = $("#DateTo").val();

            },
            complete: function (data) {
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [10, 25, 50, 100, 200, 300, 400, 500],
            [10, 25, 50, 100, 200, 300, 400, 500],
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

   





    $("#form-crud").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            dataType: 'json',
            data: formData,
            beforeSend: function () {
                $("#frmsubmit").attr("disabled", true);
            },
            success: function (data) {
                if (data.status) {
                    toastr['success'](data.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    $("#form-crud")[0].reset();
                    hideModal("add-xlarge");
                } else {
                    if (data.errors) {
                        $.each(data.errors, function (key, value) {
                            toastr['error'](value, 'Error!', {
                                closeButton: true,
                                tapToDismiss: true,
                                progressBar: true,
                            });
                        });
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
                $("#frmsubmit").attr("disabled", false);
            },
            complete: function () {
                $("#frmsubmit").attr("disabled", false);
            }
        });
    });

    function search_data() {

        table.draw();

    }

    $("#add-xlarge").on("hidden.bs.modal", function () {
        $("#form-crud")[0].reset();
        $("#id").val('');
        $("#airport").val("*");
        $('#airport').trigger('change');
        $("#form-crud-title").html("Add User");
        $("#form-crud").attr("action", "<?= base_url('users/save'); ?>");
        $(".clpassword").show();
    });
    function edit_data(id) {

        $.ajax({
            url: "<?= base_url('promotion/update'); ?>",
            dataType: 'json',
            data: "id=" + encodeURIComponent(id),
            beforeSend: function () {
                $("#form-crud-title").html("Modify User");
                $(".clpassword").hide();
            },
            success: function (res) {
                console.log(res.data);
                if (res.status) {

                    $("#id").val(id);
                    $("#CODE").val(res.data.code);
                    $("#PROMOTIONNAME").val(res.data.promotional_name);
                    $("#AGENT").val(res.data.agent).trigger('change');
                    $("#amount").val(res.data.amount);
                    $("#website").val(res.data.website).trigger('change');
                    $("#type").val(res.data.type).trigger('change');
                    $("#valid_from").val(res.valid_from);
                    $("#valid_to").val(res.valid_to);

                    $("#form-crud").attr("action", "<?= base_url('promotion/update/values'); ?>");
                    showModal("add-xlarge");
                }
            },
            error: function (xhr) {

            },
            complete: function () {

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
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('promotion/delete_record'); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: "id=" + encodeURIComponent(id),
                    beforeSend: function () {

                    },
                    success: function (data) {
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
                    error: function (xhr) {

                    },
                    complete: function () {

                    }
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>