<?php 
    $AUTH=session()->get('AUTH');
    $role_id=$AUTH['role_id'];
    $user_airport=$AUTH['airport'];
    $display="";
    if($role_id!="1")
    {
        $display="display:none;";
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
        margin: 10px 5px;
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
    #images img{
        margin-top: 10px;
        margin-right: 5px;
    }
</style>
<!-- BEGIN: Content-->
<div class="app-content content">
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
                
                    <div class="col-md-6 col-6" style="<?= $display; ?>;">
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

                    <div class="col-md-6 col-6" style="<?= $display; ?>;">
                        <div class="mb-1">
                            <label class="form-label">Agent</label>
                            <select class="form-select select2" id="agent" name="agent"></select>
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
                            <ul class="nav nav-tabs mt-1">
                              <li class="active"><a data-toggle="tab" href="#main" class="atab">Main</a></li>
                              <li><a data-toggle="tab" href="#account" class="atab">Accounts</a></li>
                            </ul>
                            <div class="tab-content">
                                <!-- main -->
                                <div id="main" class="tab-pane active"> 
                                    <!-- overflow-y:auto; -->
                                    <div class="card-datatable" style="margin: 10px;">
                                        <table class="datatables-ajax table table-responsive" id="view-datatable">
                                            <thead>
                                                <tr>
                                                    <th>Airport</th>
                                                    <th>QTY</th>
                                                    <th>Amount</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- account -->
                                <div id="account" class="tab-pane">
                                    <div class="card-datatable" style="margin: 10px;">
                                        <table class="datatables-ajax table table-responsive" id="view-datatable2">
                                            <thead>
                                                <tr>
                                                    <th>Created At</th>
                                                    <th>Airport</th>
                                                    <th>DateFrom</th>
                                                    <th>DateTo</th>
                                                    <th>QTY</th>
                                                    <th>Amount</th>
                                                    <th>GoogleCost</th>
                                                    <th>Percentage</th>
                                                    <th>ScreenShot</th>
                                                    <th style="width: 12%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<div class="modal fade text-start" id="add_gcost" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">  Add Google Cost</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="gcostForm" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body">
            <input hidden name="id" id="g_id">
            <input hidden name="airport" id="airport2">
            <input hidden name="qty" id="qty">
            <input hidden name="amount" id="amount">
            <input hidden name="DateFrom" id="dfrom">
            <input hidden name="DateTo" id="dto">
            <input hidden name="account_type" value="2">
            <div class="form-group mb-2">
                
                <div class="row">
                    <div class="col-md-4">
                        <label>Google Cost Type</label>
                        <select class="form-control" name="gtype" id="gtype">
                            <option value="1">Amount</option>
                            <option value="2">%</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label>Google Cost</label>
                        <input type="text" class="form-control" name="google_cost" id="google_cost" />
                    </div>
                </div>
                
            </div>
            <div class="form-group mb-2">
                <label>Google Cost Image</label>
                <input type="file" class="form-control" name="gcost_image[]" id="gcost_image" multiple/>
                <p class="text-center" id="images"></p>
            </div>
            <div class="form-group mb-2">
                <label>Note</label>
                <textarea name="description" id="desc" rows="5" class="form-control"></textarea>
            </div>
        </div>
         <div class="modal-footer">
             <button type="submit" class="btn btn-gradient-primary" id="btnGcost">Add</button>
         </div>
        </form>
      </div>
    </div>
</div>

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
    $('.atab').on('click', function() {
        let id = $(this).attr('href');
        $('.nav-tabs').find('li').removeClass('active');
        $('.tab-content').find('div').removeClass('active');
        $(this).parent().addClass('active');
        $(id).addClass('active');
    });

    var table;
    var table2;
    table = $('#view-datatable').DataTable({
        dom: '<l>Bfrt',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('account/supplier/get'); ?> ",
            type: 'GET',
            data: function(d) {
                    d.DateFrom =  $("#DateFrom").val();
                    d.DateTo = $("#DateTo").val(); 
                    d.role_id ="<?= $role_id; ?>";        
                    d.agent=$("#agent").val();
                    d.airport=$("#airport").val();          
            },
            complete: function(data) {
                $('.btnPaid').hide();
                if ($("#gcost").val() > 0) {
                    $('.btnPaid').show();
                }
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000, 10000],
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000, 10000],
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
    table2 = $('#view-datatable2').DataTable({
        dom: '<l>Bfrt',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('account/supplier/get2'); ?> ",
            type: 'GET',
            data: function(d) {
                    d.DateFrom =  $("#DateFrom").val();
                    d.DateTo = $("#DateTo").val(); 
                    d.airport=$("#airport").val();
                    d.role_id ="<?= $role_id; ?>";        
            },
            complete: function(data) {
                
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000, 10000],
            [10, 25, 50, 100, 200, 300, 400, 500, 1000, 5000, 10000],
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
    function search_data() 
    {
        table.draw();
        table2.draw();
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
                $('#agent').html(data.responseText);
            }
        });
    });
    // Google cost edit
    $('#view-datatable').on('click','.gcostAdd', function() {
        
        let id = $(this).data('id');
        let qty = $(this).data('qty');
        let amount = $(this).data('amount');
         let dateFrom = $("#DateFrom").val();
        let dateTo = $("#DateTo").val();

        $('#g_id').val('');
        $('#google_cost').val('');
        $('#desc').val('');
        $('#img').hide();
        $('#airport2').val(id);
        $('#qty').val(qty);
        $('#amount').val(amount);
        $('#dfrom').val(dateFrom);
        $('#dto').val(dateTo);
        $('#add_gcost').modal('show');
        $('#btnGcost').text('Add');
    });
    // Update Google cost
    $('#gcostForm').on('submit', function (e) {
        e.preventDefault();

        // Basic validation
        let id = $('#g_id').val().trim();
        let gtype = $('#gtype').val().trim();
        let googleCost = $('#google_cost').val().trim();
        let fileInput = $('#gcost_image')[0].files.length;
        let files = $('#gcost_image')[0].files;
        console.log('fileInput',fileInput)

        if (googleCost === '') {
            alert('Google Cost is required');
            return;
        }

        // if (fileInput === 0 && id === '') {
        //     alert('Google Cost Image is required');
        //     return;
        // }

        // Prepare form data
        let formData = new FormData(this);


        // Optional: Disable the submit button
        $('#btnGcost').prop('disabled', true).text('Submitting...');

        // AJAX request
        $.ajax({
            url: '<?= url_to('account/add');?>', // replace with your actual backend route
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                // console.log('res', res);
                toastr['success'](res.message, 'Success!', {
                    closeButton: true,
                    tapToDismiss: true,
                    progressBar: true
                });
                $('#gcostForm')[0].reset(); // Reset form
                hideModal("add_gcost");
                 table2.draw();
            },
            error: function (xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            },
            complete: function () {
                $('#btnGcost').prop('disabled', false).text('Add');
            }
        });
    });

    $('#view-datatable2').on('click','.editBtn', function() {
        let id = $(this).data('id');
        let qty = $(this).data('qty');
        let amount = $(this).data('amount');
        let gtype = $(this).data('gtype');
        let gcost = $(this).data('gcost');
        let desc = $(this).data('desc');
        let images = $(this).data('img');
        images = images.split(',');

        $('#g_id').val(id);
        $('#qty').val(qty);
        $('#amount').val(amount);
        $('#gtype').val(gtype);
        $('#google_cost').val(gcost);
        $('#desc').val(desc);

        $('#images').html('');
        $.each(images, function(index, image) {
            var imgElement = $('<img>', {
              src: '<?=base_url('screenshot/')?>'+image,
              width: '120px',
              alt: 'Image ' + (index + 1)
            });
            
            // Add error handling in case image fails to load
            // imgElement.on('error', function() {
            //   $(this).attr('src', 'placeholder.jpg'); // fallback image
            //   console.error('Failed to load image: ' + image);
            // });
            
            $('#images').append(imgElement);
        });
        // $('#images').attr('src','<?=base_url('screenshot/')?>'+img);
        $('#google_cost').val(gcost);
        $('#desc').val(desc);


        $('#add_gcost').modal('show');
        $('#btnGcost').text('Update');

    });

    $('#view-datatable2').on('click','.deleteBtn', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: '<?= url_to('account/delete');?>', // replace with your actual backend route
                type: 'GET',
                data: {id: id},
                dataType: 'json',
                success: function (res) {
                    // console.log('res', res);
                    toastr['success'](res.message, 'Success!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true
                    });
                    table2.draw();
                },
                error: function (xhr, status, error) {
                    let response = xhr.responseJSON || {message: 'An error occurred'};
                    toastr.error(response.message, 'Error!');
                },
                complete: function () {
                }
            });
        }

    });

    
</script>
<?= $this->endSection(); ?>