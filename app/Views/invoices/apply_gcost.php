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
    #amountData h6{
        font-weight: bold;
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
                            <select class="form-select select2 airport" id="airport" name="airport">
                                <option value="">All</option>
                                <?php $get_airports = get_airports();

                                    foreach ($get_airports as $code => $name) { ?>

                                    <option value="<?= $code; ?>"><?= $name; ?></option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-6">
                            <div class="mb-1">
                                <label class="form-label">Website</label>
                                <select class="form-select select2 product" id="website" name="website"></select>
                            </div>
                        </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label">Date From</label>
                            <input type="text" id="DateFrom" name="DateFrom" class="form-control DateFrom" />
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label">DateTo</label>
                            <input type="text" id="DateTo" name="DateTo" class="form-control DateTo" />
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="mb-1">
                            <label class="form-label" for="band_name">Google Cost</label>
                            <input type="text" id="gcost" name="gcost" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between align-items-center mx-50 row pt-0 pb-2" id="moreInputs">
                    
                </div>
                <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                    <div class="col-md-6 col-6">
                        <div class="mb-1">
                            <button type="submit" onclick="search_data();" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                    <div class="col-md-6 col-6" style="text-align: right;">
                        <div class="mb-1">
                            <button type="submit" onclick="update_gcost();" class="btn btn-primary">Apply Google Cost</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-lg-3" id="webInvoice" style="display: none;">
                <div class="col-sm-12">
                    <div class="row" style="margin-top:20px;">
                        <div class="companyData" id="companyData">
                            <div class="row" style="margin: auto">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>AIRPORT PARKING SERVICES GLOBAL LTD</h3>
                                        <h5>Davenport Green Hall Shay Lane, Hale Barns, Altrincham, England,<br> WA15 8UD</h5>
                                        <h5>info@airportparkingglobalservices.co.uk  </h5>
                                        <h5> 02081233969 </h5>
                                    </div>
                                    <div class="col-md-6 text-end g-0">
                                    <div>
                                        <div class="row">
                                            <h3>INVOICE</h3>
                                            <br>
                                            <div class="rounded-image" style="width: 150px; height: 150px; box-shadow: 0 0 10px #d6d7d7; background-color: #fff; border-radius: 50%; display: flex;
                                                    align-items: center; justify-content: center; margin-left: auto">
                                                <img src="<?= BASEURL.'logos/logo.jpg' ?>" style="width: 100% ;
                                                        height: 50%; object-fit: contain;">
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <!-- <br>
                                        <hr style="width: 76%;">
                                        <h5>Airport Parking</h5>
                                        <h5>00443940825851, dallen.airportparking@gmail.com </h5> -->
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="row g-0">
                                            <h5>Invoice No: <span id="invoiceNo"></span></h5>
                                            <!-- <hr style="width: 62%;     margin-top: 10px;"> -->
                                            <h5>Invoice Date: <span id="dateFrom"></span></h5>
                                            <h5 id="dateTo">31-07-2024</h5>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                    <div class="table-responsive border webtable">
                        <table id="webbookingTable" class="table table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                <th>Product   </th>
                                <th>QTY</th>
                                <th>Refunds</th>
                                <th>Total Amount</th>
                                <th>Download</th>
                                </tr>
                            </thead>
                            <tbody id="webbookingBody"></tbody>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
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
    $('.DateFrom').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });

    $('.DateTo').flatpickr({
        dateFormat: "m/d/Y",
        defaultDate: ["<?= date("m/d/Y"); ?>"]
    });
    $('.atab').on('click', function() {
        let id = $(this).attr('href');
        $('.nav-tabs').find('li').removeClass('active');
        $('.tab-content').find('div').removeClass('active');
        $(this).parent().addClass('active');
        $(id).addClass('active');
        $('#webInvoice').hide();
    });


    $('#airport').on('change', function() 
    {
        $.ajax({
            url: "<?= url_to('invoices/get_acairport_websites'); ?> ",
            type: 'GET',
            data: { airport : $(this).val() },
            complete: function (data) {
                // console.log('response',data.responseText);

                $('#website').html(data.responseText);
            }
        });
    });


    function search_data() 
    {
        $.ajax({
            url: "<?= url_to('invoices/get_apply_gcost'); ?> ",
            type: 'GET',
            data: {
                airport : $("#airport").val(),
                website : $("#website").val(),
                dateFrom: $("#DateFrom").val(),
                dateTo: $("#DateTo").val(),
                gcost: $("#gcost").val(),

            },
            complete: function (data) {
                console.log('response',data.responseJSON);

                $('#webInvoice').show();
                $('#opText').hide();
                $('#subText').hide();

                $('#invoiceNo').html(data.responseJSON.invoiceNo);
                $('#dateFrom').html(data.responseJSON.dateFrom);
                $('#dateTo').html(data.responseJSON.dateTo);
                $('#webbookingBody').html(data.responseJSON.data);
            }
        });
    }

    function update_gcost() 
    {
        if ($("#gcost").val() > 0) 
        {
            $.ajax({
                url: "<?= url_to('invoices/get_apply_gcost'); ?> ",
                type: 'GET',
                data: {
                    airport : $("#airport").val(),
                    operator : $("#operator").val(),
                    product : $("#product").val(),
                    dateFrom: $("#DateFrom").val(),
                    dateTo: $("#DateTo").val(),
                    gcost: $("#gcost").val(),

                },
                complete: function (data) {
                    console.log('response',data.responseJSON);

                    $('#webInvoice').show();
                    $('#opText').hide();
                    $('#subText').hide();

                    $('#invoiceNo').html(data.responseJSON.invoiceNo);
                    $('#dateFrom').html(data.responseJSON.dateFrom);
                    $('#dateTo').html(data.responseJSON.dateTo);
                    $('#webbookingBody').html(data.responseJSON.data);
                }
            });
        }else{
            alert('Please enter google cost');
        }
    }



</script>
<?= $this->endSection(); ?>