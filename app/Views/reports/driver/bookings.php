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

                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <label class="form-label" for="airport">Airport</label>
                            <select class="form-select select2" id="airport" name="airport">
                                <option value="*">All</option> 
                                <?php $get_airports = get_airports();

                                foreach ($get_airports as $code => $name) {
                                    $selected = ($code == 'DUB')? 'selected="selected"':'';
                                    echo'<option value="'.$code.'" '.$selected.'>'. $name .'</option>';

                                } ?>
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
                                            <th>Time</th>
                                            <th>Bookings</th>
                                            <th>Departures</th>
                                            <th>Returns</th>
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

<!-- Modal -->
<div class="modal fade text-start" id="view_booking" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Bookings</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">                          
            <table width="100%" class="table table-bordered">
                <thead>
                <tr>
                    <th>Referance</th>
                    <th>Airport</th>
                    <th>Passenger</th>
                </tr>
                </thead>
                <tbody id="view_booking_details">
                    
                </tbody>
            </table>
        </div>
      </div>
    </div>
</div>

<!-- END: Content-->
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">

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
        dom: 'Bfrt',
        processing: true,
        serverSide: true,
        select: true,
        searching: false, // Disables the search box
        ajax: {
            url: "<?= url_to('reports/bookings_count/get'); ?> ",
            type: 'GET',
            data: function (d) {
                d.airport = $("#airport").val();
                d.DateFrom = $("#DateFrom").val();
            },
            complete: function (data) {
                feather.replace();
            }
        },
        language: {
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
            [50, 100, 200, 300, 400, 500],
            [50, 100, 200, 300, 400, 500],
        ],
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                  columns: ':visible',
                  format: {
                    body: function(data, row, column, node) {
                      var text = node.textContent;
                      return column === 0 ? text.replace(/Open$/, '').trim() : data;
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
                      return column === 0 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function (doc) {
                    // Set table width to full page width
                    doc.defaultStyle.fontSize = 10;
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                    // Optional: Add table header styling
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 11,
                        color: 'white',
                        fillColor: '#2e4663',
                        alignment: 'left'
                    };

                    // Optional: Adjust margins
                    doc.pageMargins = [20, 20, 20, 20];
                },
                exportOptions: {
                  columns: ':visible',
                  format: {
                    body: function(data, row, column, node) {
                      var text = node.textContent;
                      return column === 0 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
        ],
        "ordering": false
    });

    function search_data() 
    {
        table.draw();
    }

    function view_bookings(type,airport,dateFrom) {
        var type = type.replace(/`/g, '');
        var airport = airport.replace(/`/g, '');
        var dateFrom = dateFrom.replace(/`/g, '');
        console.log('airport:', airport);
        console.log('date:', dateFrom);
        let ModalID = "view_booking";
        // $("#view_booking_details").html(data.html);
        // showModal(ModalID);
        $.ajax({
            url: "<?= base_url('reports/driver/bookings'); ?>", 
            type: 'GET',
            dataType: 'json',
            data: "type="+type+"&airport="+airport+"&DateFrom="+dateFrom,
            success: function(data) {
                // console.log(data.details);
                $("#"+ModalID+"_details").html(data.html);
                if(data.modal)
                 {
                    showModal(ModalID);                          
                  
                 }else{

                    if(data.status){
                        toastr['success'](data.message, 'Success!', {
                          closeButton: true,
                          tapToDismiss: true,
                          progressBar: true
                        });
                    }else{           
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

</script>
<?= $this->endSection(); ?>