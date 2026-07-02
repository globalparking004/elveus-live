<?php 
    $AUTH=session()->get('AUTH');
    $role_id=$AUTH['role_id'];
    $role_name=$AUTH['role_name'];
?>
<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<style type="text/css">
    table tbody tr:last-child{
        font-size: large;
        font-weight: bold;
    }
    table.dataTable{
        margin-top: 0!important;
    }
    .text-center{
        text-align: center;
    }
    .compareTbl{
        display: none;
    }
</style>
<!-- BEGIN: Content-->
<div class="app-content content ">
    <!-- ... (existing code) ... -->
    <div class="content-body">
        <?php if ($role_id ==1 || $role_name =='CSR') {?>
        <div class="row">
            
            <!-- First Column -->
            <div class="col-md-6">
                <div class="row">
                    <!-- Website -->
                    <div class="col-md-12 mb-2"> 
                        <div class="text-center">
                            <h1>Today's Performance</h1>
                            <div class="col-sm-12 text-center">
                                <div class="row">
                                    <h1 class="green" style="margin-bottom:0px !important; font-size:220px;">
                                        <?= $stats['completed_bookings']; ?>
                                    </h1>
                                    <h1 style="margin-top: -20px;">SALES</h1>
                                </div>
                            </div>

                            <div class="col-sm-12 text-center">
                                <div class="row">
                                    <h1 style="margin-top:30px; font-size:25px;font-weight: bold;">£
                                        <?= number_format($stats['profit'], 2); ?> Gross Profit
                                    </h1>
                                </div>
                                <div class="row">
                                    <h1 style="margin-top:10px; font-size:25px;font-weight: bold;">£
                                        <?= number_format($stats['avg'], 2); ?> Avg Per Booking
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- website -->
                    <div class="col-md-6"> 
                        <div class="text-center">
                            <h3>Website Performance</h3>
                            <div class="col-sm-12 text-center mb-2">
                                <div class="row">
                                    <h3 class="green" style="margin-bottom:0px !important; font-size:100px;">
                                        <?= $stats['completed_bookings']-$stats['completed_bookingS']; ?>
                                    </h3>
                                    <h3 style="margin-top: -20px;">SALES</h3>
                                </div>
                            </div>

                            <div class="col-sm-12 text-center">
                                <div class="row">
                                    <h1 style="font-size:14px;font-weight: bold;">£
                                        <?= number_format($stats['profitW'],2); ?> Gross Profit
                                    </h1>
                                </div>
                                <div class="row">
                                    <h1 style="margin-top:10px; font-size:14px;font-weight: bold;">£
                                        <?= number_format($stats['avgW'],2); ?> Avg Per Booking
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Supplier -->
                    <div class="col-md-6"> 
                        <div class="text-center">
                            <h3>Supplier Performance</h3>
                            <div class="col-sm-12 text-center mb-2">
                                <div class="row">
                                    <h3 class="green" style="margin-bottom:0px !important; font-size:100px;">
                                        <?= $stats['completed_bookingS']; ?> 
                                    </h3>
                                    <h3 style="margin-top: -20px;">SALES</h3>
                                </div>
                            </div>

                            <div class="col-sm-12 text-center">
                                <div class="row">
                                    <h1 style="font-size:14px;font-weight: bold;">£
                                        <?= number_format($stats['profitS'],2); ?> Gross Profit
                                    </h1>
                                </div>
                                <div class="row">
                                    <h1 style="margin-top:10px; font-size:14px;font-weight: bold;">£
                                        <?= number_format($stats['avgS'],2); ?> Avg Per Booking
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <section id="ajax-datatable" class="compareTbl">
                            <div class="card">
                                <div class="card-datatable" style="overflow-y:auto;">
                                    <table class="datatables-ajax table table-responsive" id="view-datatable3">
                                        <thead>
                                            <tr>
                                                <th colspan="3" style="text-align: center;">Website</th>
                                                <th colspan="3" style="text-align: center;">Supplier</th>
                                            </tr>
                                            <tr>
                                                <th>Airport</th>
                                                <th>QTY</th>
                                                <th>Amount</th>
                                                <th>QTY</th>
                                                <th>Amount</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section id="ajax-datatable">
                            <div class="card">
                                <div class="card-datatable">
                                    <table class="datatables-ajax table table-responsive" id="view-datatable2">
                                        <thead>
                                            <tr>
                                                <th colspan="3" style="text-align: center;">Go Comperison</th>
                                            </tr>
                                            <tr>
                                                <th>Airport</th>
                                                <th>QTY</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <!-- Second Column -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <h2 style="font-size: 50px;">Airport Capacities</h2>
                            <ul style="font-size: 30px;">
                                <?php
                                $i = 0;
                                foreach ($airportCapacity as $capacity) {
                                    $percent= round(($capacity['booking_count']/$capacity['airport_capacity'])*100,2);
                                    $class = ($percent > 90) ? 'more-90' : ($percent > 80 ? 'more-80' : '');
                                    echo '<li class="'.$class.'" style="font-size: 16px;"><b>' . $capacity['web_name'] . '</b>   ' . $capacity['booking_count'] . '/' . $capacity['airport_capacity'] . '</li>';
                                    $i++;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <h5 class="card-header text-center">Search Filter </h5>
                            <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">

                                <div class="col-md-4 col-4">
                                    <div class="mb-1">
                                        <label class="form-label" for="band_name">Date From</label>
                                        <input type="text" id="DateFrom" name="DateFrom" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                    <div class="mb-1">
                                        <label class="form-label">Time From</label>
                                        <input type="text" id="TimeFrom" name="TimeFrom" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4 col-4">
                                    <div class="mb-1">
                                        <label class="form-label" for="band_name">DateTo</label>
                                        <input type="text" id="DateTo" name="DateTo" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                    <div class="mb-1">
                                        <label class="form-label">Time To</label>
                                        <input type="text" id="TimeTo" name="TimeTo" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-12 col-12 text-center">
                                    <div class="mb-1">
                                        <button type="submit" onclick="search_data();" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                                <div class="col-md-4 col-4">
                                    <div class="mb-1">
                                        <label class="form-label">Date From2</label>
                                        <input type="text" id="DateFrom2" name="DateFrom2" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                    <div class="mb-1">
                                        <label class="form-label">Time From2</label>
                                        <input type="text" id="TimeFrom2" name="TimeFrom2" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4 col-4">
                                    <div class="mb-1">
                                        <label class="form-label">DateTo2</label>
                                        <input type="text" id="DateTo2" name="DateTo2" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                    <div class="mb-1">
                                        <label class="form-label">Time To2</label>
                                        <input type="text" id="TimeTo2" name="TimeTo2" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-12 col-12 text-center">
                                    <div class="mb-1">
                                        <button type="submit" onclick="search_data3();" class="btn btn-primary">Compare</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <section id="ajax-datatable">
                            <div class="card">
                                <div class="card-datatable" style="overflow-y:auto;">
                                    <table class="datatables-ajax table table-responsive" id="view-datatable">
                                        <thead>
                                            <tr>
                                                <th colspan="3" style="text-align: center;">Website</th>
                                                <th colspan="3" style="text-align: center;">Supplier</th>
                                            </tr>
                                            <tr>
                                                <th>Airport</th>
                                                <th>QTY</th>
                                                <th>Amount</th>
                                                <th>QTY</th>
                                                <th>Amount</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="card">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        <?php }?>
    </div>
</div>
<!-- END: Content-->
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    const dataCapacity = <?= json_encode($airportCapacity) ?>;

    // Prepare data for bar chart
    const barLabels = dataCapacity.map(item => item.web_name);
    const barData = dataCapacity.map(item => item.booking_count);
    const totalCapacityData = dataCapacity.map(item => item.airport_capacity);
    const percentageData = dataCapacity.map(item => (item.booking_count / item.airport_capacity) * 100); // Calculate % of capacity used

    const barColors = ['#FF6384', '#36A2EB'];
    const capacityColor = '#7367F0'; // Optional color for total capacity

    // Create Bar Chart
    const ctxb = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxb, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [
                {
                    label: 'Booking Count',
                    data: barData,
                    backgroundColor: capacityColor,
                },
                {
                    label: 'Total Capacity',
                    data: totalCapacityData,
                    backgroundColor:  barColors[0],
                },
                // {
                //     label: 'Percentage of Capacity Used (%)',
                //     data: percentageData,
                //     backgroundColor: '#36A2EB',
                //     barThickness: 10, // Makes percentage bars thinner
                //     yAxisID: 'percentage' // Needs a separate axis
                // }
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
                    text: 'Booking and Capacity Data'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                },
                percentage: { // Create a secondary axis for percentage
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false // Do not draw grid lines for the percentage axis
                    }
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
    $('#TimeFrom').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",  // Format for 24-hour time
        time_24hr: true,
        defaultDate: ["00:00:00"]
    });
    $('#TimeTo').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",  // Format for 24-hour time
        time_24hr: true,
        defaultDate: ["<?= date("H:i:s"); ?>"]
    });

    $('#DateFrom2').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });
    $('#DateTo2').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });
    $('#TimeFrom2').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",  // Format for 24-hour time
        time_24hr: true,
        defaultDate: ["00:00:00"]
    });
    $('#TimeTo2').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",  // Format for 24-hour time
        time_24hr: true,
        defaultDate: ["<?= date("H:i:s"); ?>"]
    });

    var table;
    table = $('#view-datatable').DataTable({
        dom: 'frt',
        processing: true,
        serverSide: true,
        select: true,
        ordering: false,
        searching: false,
        ajax: {
            url: "<?= url_to('dashboard/get_stastics'); ?>",
            type: 'GET',
            data: function(d) {
                d.DateFrom =  $("#DateFrom").val();
                d.TimeFrom =  $("#TimeFrom").val();
                d.DateTo = $("#DateTo").val();
                d.TimeTo = $("#TimeTo").val();
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
    var table2;
    table2 = $('#view-datatable2').DataTable({
        dom: 'frt',
        processing: true,
        serverSide: true,
        select: true,
        ordering: false,
        searching: false,
        ajax: {
            url: "<?= url_to('dashboard/get_go_stastics'); ?>",
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
        "lengthMenu": [
            [500, 1000, 5000, 10000],
            [500, 1000, 5000, 10000],
        ]
    });
    var table3;
    table3 = $('#view-datatable3').DataTable({
        dom: 'frt',
        processing: true,
        serverSide: true,
        select: true,
        ordering: false,
        searching: false,
        ajax: {
            url: "<?= url_to('dashboard/get_stastics2'); ?>",
            type: 'GET',
            data: function(d) {
                d.DateFrom =  $("#DateFrom2").val();
                d.TimeFrom =  $("#TimeFrom2").val();
                d.DateTo = $("#DateTo2").val();
                d.TimeTo = $("#TimeTo2").val();
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

    function search_data() {
        table.draw();
    }
    function search_data3() {
        $('.compareTbl').show();
        table.draw();
        table3.draw();
    }

</script>
<?= $this->endSection(); ?>