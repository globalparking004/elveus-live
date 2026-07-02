<?= $this->extend("layouts/base");
    $AUTH=session()->get('AUTH');
    $role_id=$AUTH['role_id'];
    $user_airport=$AUTH['airport'];
    $display="";
    if($role_id!="1")
    {
        $display="display:none;";
    } ?>
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
                            <?php if(sizeof($breadcrumb)>0) { ?>
                            <h2 class="content-header-title float-start mb-0"><?= $page_title; ?></h2>    
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <?php for($i=0;$i<sizeof($breadcrumb);$i++) { 
                                        extract($breadcrumb[$i]);
                                    ?>
                                    <?php if($link) { ?>
                                        <li class="breadcrumb-item <?= $status; ?>"><a href="<?= $href; ?>"><?= $title; ?></a></li>
                                    <?php }else{ ?>
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
                
                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="airport">Airport</label>
                                <select class="form-select select2" id="airport" name="airport">
                                    <option value="">All</option>
                                    <?php $get_airports = get_airports();

                                        foreach ($get_airports as $code => $name) { ?>

                                        <option value="<?= $code; ?>"><?= $name; ?></option>

                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-4" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="airport">Website Type</label>
                                <select class="form-select select2" id="website_type" name="website_type">
                                    <option value="">All</option>
                                    <?php echo get_website_types();?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-4 agent" style="display: none">
                            <div class="mb-1">
                                <label class="form-label" for="status">Agent</label>
                                <select class="form-select select2" id="agent" name="agent">
                                    <?php $agents = get_agents();

                                    foreach ($agents as $code => $name) { ?>

                                        <option value="<?= $code; ?>"><?= $name; ?></option>

                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-4 website" style="<?= $display; ?>;">
                            <div class="mb-1">
                                <label class="form-label" for="status">Select Website</label>
                                <select class="form-select select2" id="website" name="website" required>
                                    <option value="">Select Website</option>
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
                                <?php if(strval($role_id)>1) { ?>
                                <!-- <button type="submit" id="" onclick="print_data();" class="btn btn-info">Print Bookings</button> -->
                                <button onclick="printTable()" class="btn btn-info">Print Bookings</button>

                                <button type="submit" id="" onclick="print_dards();" class="btn btn-danger">Print Cards</button>
                                <?php  } ?>
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
                                                <th>Refund Amount</th>
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
    $('.select2').select2();
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
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('reports/refunds/get'); ?> ",
            type: 'GET',
            data: function(d) {
                    d.DateFrom =  $("#DateFrom").val();
                    d.DateTo = $("#DateTo").val(); 
                    d.role_id ="<?= $role_id; ?>";  
                    d.website_type=$("#website_type").val();
                    d.website=$("#website").val();
                    d.airport=$("#airport").val();          
            },
            complete: function(data) {
                // console.log('resp',data.responseJSON);
                pieChart.data.datasets[0].data = data.responseJSON.chartdata;
                pieChart.update();

                doughnutChart.data.datasets[0].data = data.responseJSON.chartdata;
                doughnutChart.update();

                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000],
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000],
        ],
        buttons: [
            // {
            //     extend: 'copyHtml5',
            //     exportOptions: {
            //       columns: ':visible',
            //       format: {
            //         body: function(data, row, column, node) {
            //           var text = node.textContent;
            //           return column === 10 ? text.replace(/Open$/, '').trim() : data;
            //         }
            //       }
            //     }
            // },
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

            // {
            //     extend: 'pdfHtml5',
            //     orientation: 'landscape',
            //     exportOptions: {
            //       columns: ':visible',
            //       format: {
            //         body: function(data, row, column, node) {
            //           var text = node.textContent;
            //           return column === 10 ? text.replace(/Open$/, '').trim() : data;
            //         }
            //       }
            //     }
            // },
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
    function search_data() 
    {
        table.draw();
    }
    // website type
    $('#website_type').on('change',function() {
        let val = $(this).val();
        let airport = $('#airport').val();
        console.log('val',val);
        $.ajax({
            url: "<?= url_to('reports/bookings/get_websites'); ?> ",
            type: 'GET',
            data:{val:val,airport:airport},
            // data: function(d) {
            //     d.website_type =  $(this).val();
            // },
            complete: function(data) {
                $('.agent').hide();
                $('.website').show();
                if(val == 2){
                    $('.agent').show();
                    $('.website').hide();
                }
                console.log('resp',data.responseText);
                $('#website').html(data.responseText);
            }
        });
    })
    $('#airport').on('change',function() {
        let airport = $(this).val();
        console.log('airport',airport);
        $.ajax({
            url: "<?= url_to('reports/bookings/get_websites'); ?> ",
            type: 'GET',
            data:{val:'',airport:airport},
            complete: function(data) {
                // console.log('resp',data.responseText);
                $('#website').html(data.responseText);
            }
        });
    })
</script>
<?= $this->endSection(); ?>