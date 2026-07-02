<?php 
    $AUTH=session()->get('AUTH');
    $role_id=$AUTH['role_id'];
    $role_name=$AUTH['role_name'];
    $user_airport=$AUTH['airport'];
    $display="";
    $class='3';
    if($role_id!="1" && $role_name !='CSR' && $role_name !='Pricing')
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
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Booking Reference</label>
                            <input type="text" id="reference" name="reference" class="form-control" />
                        </div>
                    </div>

                    <div class="col-md-4 col-4" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label" for="airport">Airport</label>
                            <select class="form-select select2" id="airport" name="airport">
                                <option value="*">All</option> 
                                <?php $get_airports = get_airports();

                                    foreach ($get_airports as $code => $name) { ?>

                                    <option value="<?= $code; ?>"><?= $name; ?></option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-4" style="<?= $display; ?>;">
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

                    <div class="col-md-12 col-12">
                        <div class="mb-1">
                            <div class="custom-control custom-radio">
                              <input type="radio" value="booking_at" name="filter_date" class="custom-control-input" checked="">
                              <label class="custom-control-label" for="filter_date">Booking</label>

                              <input type="radio" value="departure_at" name="filter_date" class="custom-control-input" >
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
                            <div class="card-datatable" style="margin: 10px;overflow-y:auto;">
                                <table class="datatables-ajax table table-responsive" id="view-datatable">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Website</th>
                                            <th>Airport/Car Park</th>
                                            <th>Customer Name</th>
                                            <th>Booked At</th>
                                            <th>Depart Date</th>
                                            <th>Return Date</th>
                                            <th>Amount</th>
                                            <th>Orignal</th>
                                            <th>Diff</th>
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
<!-- END: Content-->


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

    var table;
    table = $('#view-datatable').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('prices/get'); ?>",
            type: 'GET',
            data: function(d) 
            {
                d.reference = $("#reference").val();
                d.DateFrom =  $("#DateFrom").val();
                d.DateTo = $("#DateTo").val();
                d.filter_date = $("input[name='filter_date']:checked").val();  
                d.role_id ="<?= $role_id; ?>";        
                d.website=$("#website").val();
                d.airport=$("#airport").val();
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

</script>
<?= $this->endSection(); ?>