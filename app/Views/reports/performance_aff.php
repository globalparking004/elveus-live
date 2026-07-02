<?= $this->extend("layouts/base");
    $AUTH=session()->get('AUTH');
    $user_airport=$AUTH['airport'];?>

<?= $this->section("title"); ?>
	<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<style type="text/css">
    table tbody tr{
      font-weight: bold;
    }
    table tbody tr:last-child{
        font-size: large;
    }
    div.dt-buttons{
        padding: 15px 0 0!important;
    }
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

                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <button type="submit" id="" onclick="search_data();" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            <section id="ajax-datatable">
                <div class="row">
                    <div class="col-7">
                        <div class="card">
                            <div class="card-datatable" style="margin: 10px;overflow-x: scroll;">
                                <table class="datatables-ajax table table-responsive" id="view-datatable">
                                    <thead>
                                        <tr>
                                            <th>Airport</th>
                                            <th>Bookings</th>
                                            <th>Amount</th>  
                                            <th>Without Booking Fee</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="card">
                            <div class="card-datatable" style="margin: 10px;overflow-x: scroll;">
                                <table class="datatables-ajax table table-responsive" id="view-datatable2">
                                    <thead>
                                        <tr>
                                            <th>Airport</th>
                                            <th>Traffic Source</th>
                                            <th>Bookings</th>
                                            <th>Amount</th>  
                                            <th>Without Booking Fee</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-datatable" style="margin: 10px;overflow-x: scroll;">
                                <table class="datatables-ajax table table-responsive" id="view-datatable3">
                                    <thead>
                                        <tr>
                                            <th colspan="5" class="text-center"> Go Comperison</th>
                                        </tr>
                                        <tr>
                                            <th>Airport</th>
                                            <th>Traffic Source</th>
                                            <th>Bookings</th>
                                            <th>Amount</th>  
                                            <th>Without Booking Fee</th> 
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
<div>
<!-- Modal -->
<div class="modal fade text-start" id="view_airport" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Supplier Booking By Airports</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">                          
            <table width="100%" class="table table-bordered">
                <thead>
                <tr>
                    <th>Airport</th>
                    <th>Bookings</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody id="view_airport_details"></tbody>
            </table>
        </div>
      </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>

<script type="text/javascript">
    $('.select2').select2();
    $('#DateFrom').flatpickr({
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

    $('#DateTo').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });
    $('#TimeTo').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",  // Format for 24-hour time
        time_24hr: true,
        defaultDate: ["23:59:59"]
        // defaultDate: ["<?= date("H:i:s"); ?>"]
    });

    var table;
    var table2;
    table = $('#view-datatable').DataTable({
        dom: 'Bfrt',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('reports/aff-performance/get-web'); ?> ",
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function (doc) {
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
        ],
        "ordering": false,
    });
    table2 = $('#view-datatable2').DataTable({
        dom: 'Brt',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('reports/aff-performance/get'); ?> ",
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
            [25, 50, 100, 200, 300, 400, 500],
            [25, 50, 100, 200, 300, 400, 500],
        ],
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                  columns: ':visible',
                  format: {
                    body: function(data, row, column, node) {
                      var text = node.textContent;
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function (doc) {
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
        ],
        "ordering": false,
    });

    table3 = $('#view-datatable3').DataTable({
        dom: 'Brt',
        processing: true,
        serverSide: true,
        select: true,
        ajax: {
            url: "<?= url_to('reports/aff-performance/get-go'); ?> ",
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
            [25, 50, 100, 200, 300, 400, 500],
            [25, 50, 100, 200, 300, 400, 500],
        ],
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                  columns: ':visible',
                  format: {
                    body: function(data, row, column, node) {
                      var text = node.textContent;
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function (doc) {
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
                      return column === 2 ? text.replace(/Open$/, '').trim() : data;
                    }
                  }
                }
            },
        ],
        "ordering": false,
    });

    function search_data() 
    {
        table.draw();
        table2.draw();
        table3.draw();
    }
                
    // websites accoring to airport
    $('#airport').on('change',function() {
        let airport = $(this).val();
        console.log('airport',airport);
        $.ajax({
            url: "<?= url_to('reports/bookings/get_websites'); ?> ",
            type: 'GET',
            data:{val:'',airport:airport},
            complete: function(data) {
                console.log('resp',data.responseText);
                $('#website').html(data.responseText);
            }
        });
    })
    $('.view-airport').on('click',function() {
        let source = $(this).data('source');
        console.log('source: ',source);
    });

    function view_airport(source,datef,datet)
    {
        var source = source.replace(/`/g, '');
        var datef = datef.replace(/`/g, '');
        var datet = datet.replace(/`/g, '');
        let ModalID = "view_airport";
        // console.log('source: ',source);
        // console.log('datef: ',datef);
        // console.log('datet: ',datet);
        $.ajax({
            url: "<?= base_url('reports/get_airport_by_supplier'); ?>", 
            type: 'GET',
            dataType: 'json',
            data: "source="+source+"&DateFrom="+datef+"&DateTo="+datet,
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