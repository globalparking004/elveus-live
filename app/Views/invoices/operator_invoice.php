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
                <ul class="nav nav-tabs mt-1">
                  <li class="active"><a data-toggle="tab" href="#website" class="atab">Website</a></li>
                  <li><a data-toggle="tab" href="#supplier" class="atab">Supplier</a></li>
                  <li><a data-toggle="tab" href="#invoice" class="atab">Invoice</a></li>
                </ul>

                <div class="tab-content">
                    <div id="website" class="tab-pane active">
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
                            <div class="col-md-3 col-3"> 
                                <div class="mb-1">
                                    <label class="form-label">Product</label>
                                    <select class="form-select select2 product" id="product" name="product"></select>
                                </div>
                            </div>
                            <div class="col-md-3 col-3">
                                <div class="mb-1">
                                    <label class="form-label">Operator</label>
                                    <select class="form-select select2" id="operator" name="operator">
                                        <option value="*">All</option>
                                            <?php foreach($operators as $r){
                                            $description=$r->description;
                                            $id=$r->id;
                                            echo "<option value='$id'>$description</option>";
                                            }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Date From</label>
                                    <input type="text" id="DateFrom" name="DateFrom" class="form-control DateFrom" />
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">DateTo</label>
                                    <input type="text" id="DateTo" name="DateTo" class="form-control DateTo" />
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Google Cost including TAX</label>
                                    <input type="text" id="gCost" name="gCost" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Google Cost On Refunds</label>
                                    <input type="text" id="gCostRefund" name="gCostRefund" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Dispute Amount</label>
                                    <input type="text" id="disAmount" name="disAmount" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Office Cost</label>
                                    <input type="text" id="offCost" name="offCost" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <button id="moreBtn" type="button" class="btn btn-primary waves-effect waves-float waves-light">More</button>
                            </div>
                        </div>
                        <div class="row justify-content-between align-items-center mx-50 row pt-0 pb-2" id="moreInputs">
                            
                        </div>
                        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <button type="submit" id="" onclick="search_data();" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="supplier" class="tab-pane">
                        <h5 class="card-header">Search Filter </h5>
                    
                        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                            <div class="col-md-4 col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="airport">Airport</label>
                                    <select class="form-select select2 airport" id="airport2" name="airport">
                                        <option value="*">All</option>
                                        <?php $get_airports = get_airports();

                                            foreach ($get_airports as $code => $name) { ?>

                                            <option value="<?= $code; ?>"><?= $name; ?></option>

                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Product</label>
                                    <select class="form-select select2 product" id="product2" name="product"></select>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Supplier</label>
                                    <select class="form-select select2" id="supplier2" name="supplier">
                                        <option value="*">All</option>
                                        <?php $agents = get_agents();
                                        foreach ($agents as $code => $name) {
                                            echo "<option value='".$code."'>".$name."</option>";
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Date From</label>
                                    <input type="text" id="DateFrom2" name="DateFrom" class="form-control DateFrom" />
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">DateTo</label>
                                    <input type="text" id="DateTo2" name="DateTo" class="form-control DateTo" />
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Google Cost including TAX</label>
                                    <input type="text" id="gCost2" name="gCost" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Google Cost On Refunds</label>
                                    <input type="text" id="gCostRefund2" name="gCostRefund" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Dispute Amount</label>
                                    <input type="text" id="disAmount2" name="disAmount" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="mb-1">
                                    <label class="form-label">Office Cost</label>
                                    <input type="text" id="offCost2" name="offCost" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <button id="moreBtn2" type="button" class="btn btn-primary waves-effect waves-float waves-light">More</button>
                            </div>
                        </div>
                        <div class="row justify-content-between align-items-center mx-50 row pt-0 pb-2" id="moreInputs2">
                            
                        </div>
                        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <button type="submit" id="" onclick="search_supplier_data();" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="invoice" class="tab-pane">
                        <h5 class="card-header">Search Filter </h5>
                    
                        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="airport">Airport</label>
                                    <select class="form-select select2 airport" id="airport3" name="airport">
                                        <option value="*">All</option>
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
                                    <select class="form-select select2 product" id="website3" name="website3"></select>
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="mb-1">
                                    <label class="form-label">Year</label>
                                    <select class="form-select select2" id="year" name="year">
                                        <?= get_invoice_years()?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="mb-1">
                                    <label class="form-label">Month</label>
                                    <select class="form-select select2" id="month" name="month">
                                        <?= get_invoice_month()?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="mb-1">
                                    <label class="form-label">Days</label>
                                    <select class="form-select select2" id="days" name="days">
                                        <?= get_invoice_days()?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="mb-1">
                                    <label class="form-label">Office Cost</label>
                                    <input type="text" id="offCost3" name="offCost" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <button id="moreBtn3" type="button" class="btn btn-primary waves-effect waves-float waves-light">More</button>
                            </div>
                        </div>
                        <div class="row justify-content-between align-items-center mx-50 row pt-0 pb-2" id="moreInputs3">
                            
                        </div>
                        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <button type="submit" id="" onclick="search_invoice_data();" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <!-- </form> -->
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
                                <th id="product-head">Product</th>
                                <th>QTY</th>
                                <th>Collected</th>
                                <th>noShow</th>
                                <th>Refunds</th>
                                <th id="gcost-head">Google Cost</th>
                                <th>Total Amount</th>
                                <th>Collected Amount</th>
                                <th>Download</th>
                                </tr>
                            </thead>
                            <tbody id="webbookingBody"></tbody>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="row" style="margin-top:20px;">
                        <div class="amountData" id="amountData">
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row" hidden>
                                                <div class="col-md-8 text-end">
                                                    <h6>Total Income: </h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h6 id="saleTotal"></h6> 
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="row" id="opText" style="display: none;">
                                                <div class="col-md-10 offset-md-2">
                                                    <div class="row">
                                                        <div class="col-md-8 text-end">
                                                            <h6>Supplier Commission: </h6>
                                                        </div>
                                                        <div class="col-md-3 text-end">
                                                            <h6 id="operatorTotal"> 0</h6>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="subText" style="display: none;">
                                                <div class="col-md-10 offset-md-2">
                                                    <div class="row">
                                                        <div class="col-md-8 text-end">
                                                            <h6>Total: </h6>
                                                        </div>
                                                        <div class="col-md-3 text-end">
                                                            <h6 id="subTotal"> 0</h6>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <h6>Less</h6>
                                        <hr>
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                                <div class="col-md-8 text-end">
                                                    <h6>10% of Gross website Income: </h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h6 id="grossIncome"> 0</h6>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                                <div class="col-md-8 text-end">
                                                    <h6>Google Cost including TAX: </h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h6 id="googleCost"> 0</h6>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                                <div class="col-md-8 text-end">
                                                    <h6>Google Cost on refunds: </h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h6 id="googleCostRefund"> 0</h6>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                                <div class="col-md-8 text-end">
                                                    <h6>Dispute Amount: </h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h6 id="disputeAmount"> 0</h6>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                                <div class="col-md-8 text-end">
                                                    <h6>Office Cost: </h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h6 id="officeCost"> 0</h6>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="newTax"></div>
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                            <hr style="width: 88%;">
                                                <div class="col-md-8 text-end">
                                                    <h4>Total Payable: </h4>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h3 id="payable"> 0</h3>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="row">
                                            <hr style="width: 88%;">
                                                <div class="col-md-8 text-end"></div>
                                                <div class="col-md-3 text-end">
                                                    <form class="form-crud" action="<?= base_url('invoices/operator/generate'); ?>" method="GET">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="filter_type" id="filter_type" value="departure_at">
                                                        <input type="hidden" name="isSupplier" id="isSupplier">
                                                        <input type="hidden" name="ginvoiceNo" id="ginvoiceNo">
                                                        <input type="hidden" name="gairport" id="gairport">
                                                        <input type="hidden" name="gproduct" id="gproduct">
                                                        <input type="hidden" name="gsaleTotal" id="gsaleTotal">
                                                        <input type="hidden" name="goperatorTotal" id="goperatorTotal">
                                                        <input type="hidden" name="gsubTotal" id="gsubTotal">
                                                        <input type="hidden" name="ggoogleCost" id="ggoogleCost">
                                                        <input type="hidden" name="ggoogleCostRefund" id="ggoogleCostRefund">
                                                        <input type="hidden" name="gdisputeAmount" id="gdisputeAmount">
                                                        <input type="hidden" name="gofficeCost" id="gofficeCost">
                                                        <input type="hidden" name="gpayable" id="gpayable">
                                                        <input type="hidden" name="ginputLabels" id="ginputLabels">
                                                        <input type="hidden" name="ginputValues" id="ginputValues">
                                                        <input type="hidden" name="gdateFrom" id="gdateFrom">
                                                        <input type="hidden" name="gdateTo" id="gdateTo">
                                                        <input type="hidden" name="gsupplier" id="gsupplier">
                                                        <button type="submit" id="generateBtn2" class="btn btn-primary waves-effect waves-float waves-light">Generate</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    let count=0;
    let count2=0;
    $('#moreBtn').on('click', function()
    {
        count++;
        let html ='<div class="col-md-6 col-6">'+
                        '<div class="mb-1">'+
                            '<input type="text" data-id="extra'+count+'" name="inputLabel[]" class="form-control newlabel mb-1" placeholder="Label of new field'+count+'"/>'+
                            '<input type="text" id="extra'+count+'" name="inputVal[]" class="form-control newvalue" placeholder="Value of new field'+count+'"/>'+
                        '</div>'+
                    '</div>';
        $('#moreInputs').append(html);
    });

    $('#moreBtn2').on('click', function()
    {
        count2++;
        let html ='<div class="col-md-6 col-6">'+
                        '<div class="mb-1">'+
                            '<input type="text" data-id="extra'+count+'" name="inputLabel2[]" class="form-control newlabel2 mb-1" placeholder="Label of new field'+count+'"/>'+
                            '<input type="text" id="extra'+count+'" name="inputVal2[]" class="form-control newvalue2" placeholder="Value of new field'+count+'"/>'+
                        '</div>'+
                    '</div>';
        $('#moreInputs2').append(html);
    });

    $('#moreBtn3').on('click', function()
    {
        count2++;
        let html ='<div class="col-md-6 col-6">'+
                        '<div class="mb-1">'+
                            '<input type="text" data-id="extra'+count+'" name="inputLabel3[]" class="form-control newlabel3 mb-1" placeholder="Label of new field'+count+'"/>'+
                            '<input type="text" id="extra'+count+'" name="inputVal3[]" class="form-control newvalue3" placeholder="Value of new field'+count+'"/>'+
                        '</div>'+
                    '</div>';
        $('#moreInputs3').append(html);
    });

    $('#airport').on('change', function() 
    {
        $.ajax({
            url: "<?= url_to('invoices/get-products'); ?> ",
            type: 'GET',
            data: { airport : $(this).val(), operator: '' },
            complete: function (data) {
                // console.log('response',data.responseText);

                $('#product').html(data.responseText);
            }
        });
    });

    $('#airport2').on('change', function() 
    {
        $.ajax({
            url: "<?= url_to('invoices/get-products'); ?> ",
            type: 'GET',
            data: { airport : $(this).val(), operator: '' },
            complete: function (data) {
                $('#product2').html(data.responseText);
            }
        });
    });

    $('#airport3').on('change', function() 
    {
        let airport = $(this).val();
        $.ajax({
            url: "<?= url_to('invoices/get_acairport_websites'); ?> ",
            type: 'GET',
            data: { airport : airport },
            complete: function (data) {

                $('#website3').html(data.responseText);
            }
        });
    });

    function search_data() 
    {
        var inputLabels = $('input[name="inputLabel[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        var inputValues = $('input[name="inputVal[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array
        
        if (inputLabels == '') {
            inputLabels = '';
        }
        if (inputValues == '') {
            inputValues = '';
        }
       
        $.ajax({
            url: "<?= url_to('invoices/operator/get'); ?> ",
            type: 'GET',
            data: {
                Airport : $("#airport").val(),
                operator : $("#operator").val(),
                Product : $("#product").val(),
                DateFrom: $("#DateFrom").val(),
                DateTo: $("#DateTo").val(),
                googleCost: $("#gCost").val(),
                googleCostRefund: $("#gCostRefund").val(),
                disputeAmount: $("#disAmount").val(),
                officeCost: $("#offCost").val(),
                inputLabels: inputLabels,
                inputValues: inputValues,

            },
            complete: function (data) {
                console.log('response',data.responseJSON);

                $('#webInvoice').show();
                $('#opText').hide();
                $('#subText').hide();
                $('#gcost-head').show();

                $('#invoiceNo').html(data.responseJSON.invoiceNo);
                $('#dateFrom').html(data.responseJSON.dateFrom);
                $('#dateTo').html(data.responseJSON.dateTo);
                $('#webbookingBody').html(data.responseJSON.data);

                let saleTotal = data.responseJSON.saleTotal;
                let grossIncome = ((parseFloat(saleTotal)/100)*10).toFixed(2);

                if (saleTotal == 0) {
                    grossIncome=0;
                }
                $('#saleTotal').html(saleTotal);

                
                $('#grossIncome').html('('+grossIncome+')');
                

                let googleCost = data.responseJSON.googleCost;
                $('#googleCost').html('('+googleCost+')');

                let googleCostRefund = data.responseJSON.googleCostRefund;
                $('#googleCostRefund').html('('+googleCostRefund+')');

                let refunds = data.responseJSON.refunds;

                let disputeAmount = data.responseJSON.disputeAmount;
                $('#disputeAmount').html('('+disputeAmount+')');

                let officeCost = data.responseJSON.officeCost;
                $('#officeCost').html('('+officeCost+')');

                let extra_price = data.responseJSON.extra_price;

                let inputs = data.responseJSON.inputs;
                let html = '';
                $('#newTax').html('');
                $.each(inputs, function(index, item) {
                    html += '<div class="row ">'+
                            '<div class="col-md-10 offset-md-2">'+
                                '<div class="row">'+
                                    '<div class="col-md-8 text-end">'+
                                        '<h6>'+ item.label +': </h6>'+
                                    '</div>'+
                                    '<div class="col-md-3 text-end">'+
                                        '<h6>'+ item.value +'</h6>'+
                                        '<hr>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                });
                $('#newTax').append(html);

                let payable = (parseFloat(saleTotal) - parseFloat(grossIncome) - parseFloat(googleCost) - parseFloat(googleCostRefund) - parseFloat(refunds) - parseFloat(disputeAmount) - parseFloat(officeCost) - parseFloat(extra_price)).toFixed(2);
                if (saleTotal == 0) {
                    payable=0;
                }
                $('#payable').html(payable);

                $('#isSupplier').val('');
                $('#ginvoiceNo').val(data.responseJSON.invoiceNo);
                $('#gairport').val(data.responseJSON.airport);
                $('#gproduct').val(data.responseJSON.product);
                $('#gdateFrom').val(data.responseJSON.dateFrom);
                $('#gdateTo').val(data.responseJSON.dateTo);
                $('#gsaleTotal').val(saleTotal);
                $('#goperatorTotal').val(0);
                $('#gsubTotal').val(0);
                $('#ggoogleCost').val(googleCost);
                $('#ggoogleCostRefund').val(googleCostRefund);
                $('#gdisputeAmount').val(disputeAmount);
                $('#gofficeCost').val(officeCost);
                $('#gpayable').val(payable);
                $('#ginputLabels').val(inputLabels);
                $('#ginputValues').val(inputValues);
            }
        });
    }

    function search_supplier_data() 
    {
        var inputLabels = $('input[name="inputLabel2[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        var inputValues = $('input[name="inputVal2[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        if (inputLabels == '') {
            inputLabels = '';
        }
        if (inputValues == '') {
            inputValues = '';
        }
        $.ajax({
            url: "<?= url_to('invoices/operator/get-supplier-dinvoice'); ?> ",
            type: 'GET',
            data: {
                airport : $("#airport2").val(),
                product : $("#product2").val(),
                supplier : $("#supplier2").val(),
                dateFrom: $("#DateFrom2").val(),
                dateTo: $("#DateTo2").val(),
                googleCost: $("#gCost2").val(),
                googleCostRefund: $("#gCostRefund2").val(),
                disputeAmount: $("#disAmount2").val(),
                officeCost: $("#offCost2").val(),
                inputLabels: inputLabels,
                inputValues: inputValues,

            },
            complete: function (data) {
                console.log('response',data.responseJSON);

                $('#webInvoice').show();
                $('#opText').show();
                $('#subText').show();
                $('#gcost-head').show();

                $('#invoiceNo').html(data.responseJSON.invoiceNo);
                $('#dateFrom').html(data.responseJSON.dateFrom);
                $('#dateTo').html(data.responseJSON.dateTo);
                $('#webbookingBody').html(data.responseJSON.data);

                let saleTotal = data.responseJSON.saleTotal;
                let operatorTotal = data.responseJSON.operatorTotal;
                let subTotal = data.responseJSON.subTotal;
                let grossIncome = ((parseFloat(subTotal)/100)*10).toFixed(2);

                if (saleTotal == 0) {
                    grossIncome=0;
                }
                $('#saleTotal').html(saleTotal);
                $('#operatorTotal').html('('+operatorTotal+')');
                $('#subTotal').html(subTotal);

                $('#grossIncome').html('('+grossIncome+')');
                

                let googleCost = data.responseJSON.googleCost;
                $('#googleCost').html('('+googleCost+')');

                let googleCostRefund = data.responseJSON.googleCostRefund;
                $('#googleCostRefund').html('('+googleCostRefund+')');

                let refunds = data.responseJSON.refunds;

                let disputeAmount = data.responseJSON.disputeAmount;
                $('#disputeAmount').html('('+disputeAmount+')');

                let officeCost = data.responseJSON.officeCost;
                $('#officeCost').html('('+officeCost+')');

                let extra_price = data.responseJSON.extra_price;

                let inputs = data.responseJSON.inputs;
                let html = '';
                $.each(inputs, function(index, item) {
                    html += '<div class="row ">'+
                            '<div class="col-md-10 offset-md-2">'+
                                '<div class="row">'+
                                    '<div class="col-md-8 text-end">'+
                                        '<h6>'+ item.label +': </h6>'+
                                    '</div>'+
                                    '<div class="col-md-3 text-end">'+
                                        '<h6>('+ item.value +')</h6>'+
                                        '<hr>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                });
                $('#newTax').append(html);

                let payable = (parseFloat(subTotal) - parseFloat(grossIncome) - parseFloat(googleCost) - parseFloat(googleCostRefund) - parseFloat(refunds) - parseFloat(disputeAmount) - parseFloat(officeCost) - parseFloat(extra_price)).toFixed(2);
                if (saleTotal == 0) {
                    payable=0;
                }
                $('#payable').html(payable);

                $('#isSupplier').val('yes');
                $('#gsupplier').val($("#supplier").val());
                $('#ginvoiceNo').val(data.responseJSON.invoiceNo);
                $('#gairport').val(data.responseJSON.airport);
                $('#gproduct').val(data.responseJSON.product);
                $('#gdateFrom').val(data.responseJSON.dateFrom);
                $('#gdateTo').val(data.responseJSON.dateTo);
                $('#gsaleTotal').val(saleTotal);
                $('#goperatorTotal').val(operatorTotal);
                $('#gsubTotal').val(subTotal);
                $('#ggoogleCost').val(googleCost);
                $('#ggoogleCostRefund').val(googleCostRefund);
                $('#gdisputeAmount').val(disputeAmount);
                $('#gofficeCost').val(officeCost);
                $('#gpayable').val(payable);
                $('#ginputLabels').val(inputLabels);
                $('#ginputValues').val(inputValues);
            }
        });
    }

    function search_invoice_data() 
    {
        var inputLabels = $('input[name="inputLabel3[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        var inputValues = $('input[name="inputVal3[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        if (inputLabels == '') {
            inputLabels = '';
        }
        if (inputValues == '') {
            inputValues = '';
        }
        $.ajax({
            url: "<?= url_to('invoices/operator/get-invoice-data'); ?> ",
            type: 'GET',
            data: {
                airport : $("#airport3").val(),
                website : $("#website3").val(),
                year: $("#year").val(),
                month: $("#month").val(),
                days: $("#days").val(),
                officeCost: $("#offCost3").val(),
                inputLabels: inputLabels,
                inputValues: inputValues,

            },
            complete: function (data) {
                console.log('response',data.responseJSON);

                $('#webInvoice').show();
                // $('#opText').show();
                $('#subText').show();
                $('#gcost-head').show();
                $('#product-head').text('Date');

                $('#invoiceNo').html(data.responseJSON.invoiceNo);
                $('#dateFrom').html(data.responseJSON.dateFrom);
                $('#dateTo').html(data.responseJSON.dateTo);
                $('#webbookingBody').html(data.responseJSON.data);

                let saleTotal = data.responseJSON.saleTotal;
                let operatorTotal = data.responseJSON.operatorTotal;
                let subTotal = data.responseJSON.subTotal;
                let grossIncome = ((parseFloat(subTotal)/100)*10).toFixed(2);

                if (saleTotal == 0) {
                    grossIncome=0;
                }
                $('#saleTotal').html(saleTotal);
                $('#operatorTotal').html('('+operatorTotal+')');
                $('#subTotal').html(subTotal);

                $('#grossIncome').html('('+grossIncome+')');
                

                let googleCost = data.responseJSON.googleCost;
                $('#googleCost').html('('+googleCost+')');

                let googleRefundCost = data.responseJSON.googleRefundCost;
                $('#googleCostRefund').html('('+googleRefundCost+')');

                let refunds = data.responseJSON.refunds;

                let disputeAmount = data.responseJSON.disputeAmount;
                $('#disputeAmount').html('('+disputeAmount+')');

                let officeCost = data.responseJSON.officeCost;
                $('#officeCost').html('('+officeCost+')');

                let extra_price = data.responseJSON.extra_price;

                let inputs = data.responseJSON.inputs;
                let html = '';
                $.each(inputs, function(index, item) {
                    html += '<div class="row ">'+
                            '<div class="col-md-10 offset-md-2">'+
                                '<div class="row">'+
                                    '<div class="col-md-8 text-end">'+
                                        '<h6>'+ item.label +': </h6>'+
                                    '</div>'+
                                    '<div class="col-md-3 text-end">'+
                                        '<h6>('+ item.value +')</h6>'+
                                        '<hr>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                });
                $('#newTax').append(html);

                let payable = (parseFloat(subTotal) - parseFloat(grossIncome) - parseFloat(googleCost) - parseFloat(googleRefundCost) - parseFloat(refunds) - parseFloat(disputeAmount) - parseFloat(officeCost) - parseFloat(extra_price)).toFixed(2);
                if (saleTotal == 0) {
                    payable=0;
                }
                $('#payable').html(payable);

                $('#isSupplier').val('');
                $('#gsupplier').val('');
                $('#ginvoiceNo').val(data.responseJSON.invoiceNo);
                $('#gairport').val(data.responseJSON.airport);
                $('#gproduct').val(data.responseJSON.product);
                $('#gdateFrom').val(data.responseJSON.dateFrom);
                $('#gdateTo').val(data.responseJSON.dateTo);
                $('#gsaleTotal').val(saleTotal);
                $('#goperatorTotal').val(operatorTotal);
                $('#gsubTotal').val(subTotal);
                $('#ggoogleCost').val(googleCost);
                $('#ggoogleCostRefund').val(googleCostRefund);
                $('#gdisputeAmount').val(disputeAmount);
                $('#gofficeCost').val(officeCost);
                $('#gpayable').val(payable);
                $('#ginputLabels').val(inputLabels);
                $('#ginputValues').val(inputValues);
            }
        });
    }

    $('#generateBtn').on('click', function()  
    {
        var inputLabels = $('input[name="inputLabel[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        var inputValues = $('input[name="inputVal[]"]').map(function() {
          return $(this).val();  // Get the value of each input
        }).get();  // Convert the jQuery object to a regular array

        if (inputLabels == '') {
            inputLabels = '';
        }
        if (inputValues == '') {
            inputValues = '';
        }
        $.ajax({
            url: "<?= url_to('invoices/admin/generate'); ?> ",
            type: 'GET',
            data: {
                airport : $("#airport").val(),
                website : $("#website").val(),
                product : $("#product").val(),
                dateFrom: $("#DateFrom").val(),
                dateTo: $("#DateTo").val(),
                googleCost: $("#gCost").val(),
                googleCostRefund: $("#gCostRefund").val(),
                disputeAmount: $("#disAmount").val(),
                officeCost: $("#offCost").val(),
                inputLabels: inputLabels,
                inputValues: inputValues,

            },
            complete: function (data) {
                console.log('response',data.responseJSON);
                // $('#webInvoice').show();

                // $('#invoiceNo').html(data.responseJSON.invoiceNo);
                // $('#dateFrom').html(data.responseJSON.dateFrom);
                // $('#dateTo').html(data.responseJSON.dateTo);
                // $('#webbookingBody').html(data.responseJSON.data);

                // $('#saleTotal').html(0);
                // $('#grossIncome').html(0);
                // $('#googleCost').html(0);

                // $('#googleCostRefund').html(0);

                // $('#refunds').html(0);
                // $('#disputeAmount').html(0);

                // $('#officeCost').html(0);

                // $('#newTax').html('');

                // $('#payable').html(0);
            }
        });
    });



</script>
<?= $this->endSection(); ?>