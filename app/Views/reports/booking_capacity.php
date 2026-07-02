<?php
$AUTH = session()->get('AUTH');
$role_id = $AUTH['role_id'];
$display = "";
if ($role_id != "1") {
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
                   
                    <div class="col-md-6 col-6" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="contactNumber">Operator</label>
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
                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <label class="form-label">Product</label>
                            <select class="form-select select2" id="product" name="product">
                                <option value="">Select Product</option>
                                <option value="All">All</option>
                                <?php foreach ($websites as $website) {
                                    $name = $website->name;
                                    $id = $website->id;
                                    echo "<option value='$id'>$name</option>";
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
                    
                    <div class="col-md-3 col-6">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">DateTo</label>
                            <input type="text" id="DateTo" name="DateTo" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="mb-1">
                            <label class="form-label">Time</label>
                            <select class="form-select select2" id="TimeFrom" name="TimeFrom">
                                <?php $get_shift_time = get_booking_shift_time();

                                foreach ($get_shift_time as $code => $name) { ?>

                                    <option value="<?= $code; ?>"><?= $name; ?></option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 d-none">
                        <div class="mb-1">
                            <label class="form-label">TimeTo</label>
                            <select class="form-select select2" id="TimeTo" name="TimeTo">
                                <?php $get_shift_time = get_booking_shift_time();

                                foreach ($get_shift_time as $code => $name) { ?>

                                    <option value="<?= $code; ?>"><?= $name; ?></option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <button type="submit" id="" onclick="search_data();" class="btn btn-primary">Search</button>
                            <!-- <button type="submit" class="btn btn-primary">Search</button> -->
                            <?php if (strval($role_id) > 1) { ?>
                                <button type="submit" id="" onclick="print_data();" class="btn btn-info">Print
                                    Bookings</button>
                                <button type="submit" id="" onclick="print_dards();" class="btn btn-danger">Print
                                    Cards</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>

            <section id="ajax-datatable">
                <div class="row">
                    <div class="col-4">
                        <div class="card">
                            <div class="card-datatable" style="margin: 10px;overflow-y:auto;">
                                <table class="datatables-ajax table table-responsive" id="view-datatable">
                                    <thead>
                                        <tr>
                                            <th>Date From</th>
                                            <!-- <th>Date To</th> -->
                                            <th>Capacity Full</th>
                                            <th>All Capacity</th>
                                            <th>Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card">
                            <canvas id="reportChart"></canvas>
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

<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    let count = $('span.bg-success').text();
    const ctx4 = document.getElementById('reportChart');

    var myChart = new Chart(ctx4, {
        type: 'bar',
        data: {
          labels: [<?php echo date('m/d/Y') ?>],
          datasets: [{
            label: '',
            data:  [10],
            borderWidth: 1
          }]
        },
        options: {
            scales: {
                y: {
                  beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    position: 'average',
                }
            }
        }
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
            url: "<?= url_to('reports/booking_capacity/report'); ?> ",
            type: 'GET',
            data: function (d) {
                d.operator = $("#operator").val();
                d.product = $("#product").val();
                // d.promotional_name = $("#promotional_name").val();
                // d.website = $("#website").val();

                d.DateFrom = $("#DateFrom").val();
                d.TimeFrom = $("#TimeFrom").val();
                d.DateTo = $("#DateTo").val();
                // d.TimeTo = $("#TimeTo").val();

            },
            complete: function (data) {
                console.log('response',data.responseJSON);
                myChart.data.labels = data.responseJSON.labels;
                myChart.data.datasets[0].data = data.responseJSON.bcount;
                myChart.update();

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

    function search_data() {

        table.draw();

    }

</script>
<?= $this->endSection(); ?>