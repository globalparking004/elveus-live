<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Add some basic styling */
        body { font-family: Arial, sans-serif; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #F3F2F7; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        table { width: 100%; line-height: inherit; text-align: left; }
        table td { 
            padding: 8px; 
            color: #6e6b7b;
            display: table-cell;
            vertical-align: inherit;
            unicode-bidi: isolate;
            font-size: .757rem;
        }
        table>:not(:last-child)>:last-child>* {
            border-bottom-color: #EBE9F1;
        }
        table thead th {
            padding: 8px;
            background: #F3F2F7; 
            vertical-align: top;
            text-transform: uppercase;
            font-size: .757rem;
            letter-spacing: .5px;
            text-align:left;
            color: #6e6b7b
        }
        table tbody td{
            border-bottom: 1px solid #EBE9F1;
        }
        table tfoot td{
            padding: 7px; 
            color: #6e6b7b;
            text-align: right;
        }
        th.price{
            text-align: left;
            font-weight: bold;
        }
        td.total{
            font-size: 1rem;
            border-top: 1px solid #6e6b7b;
        }
        row{
            max-width: 100%;
            margin-bottom: 20px;
        }
        h1,h2,h3,h4,h5,h6{
            margin-top: 0;
            margin-bottom: .5rem;
            font-family: inherit;
            font-weight: 500;
            line-height: 1.2;
            color: #5E5873;
        }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="row">
            <div class="col-md-6" style="width: 50%;float: left;">
                <div style="height: 170px;">
                    <h3>AIRPORT PARKING SERVICES GLOBAL LTD</h3>
                    <h5>Davenport Green Hall Shay Lane, Hale Barns, Altrincham, England, WA15 8UD</h5>
                    <h5>info@airportparkingglobalservices.co.uk  </h5>
                    <h5> 02081233969 </h5>
                </div>
                <!-- <div class="row">
                    <div class="col-md-12" style="width: 100%;">
                        <br>
                        <h5>Airport Parking</h5>
                        <h5>00443940825851, <br>dallen.airportparking@gmail.com </h5>
                    </div>
                </div> -->
            </div>
            <div class="col-md-6 text-end g-0" style="width: 50%;float: right;">
                <div class="row">
                    <h3 style="text-align: right;">INVOICE</h3>
                    <br>
                    <div class="rounded-image" style="width: 150px; height: 150px; box-shadow: 0 0 10px #d6d7d7; background-color: #fff; border-radius: 50%; display: flex;align-items: center; justify-content: center; margin-left: auto">
                        <img src="<?= BASEURL  .'/logos/logo.jpg' ?>" alt="Logo" style="width: 100% ;object-fit: contain;">
                    </div>
                </div>
                <div class="row" style="text-align: right;">
                    <h5>Invoice No: <span id="invoiceNo"><?= $invoiceNo?></span></h5>
                    <h5>Invoice Date: <span id="dateFrom"><?= $dateFrom?></span></h5>
                    <h5 id="dateTo"><?= $dateTo?></h5>
                    <br>
                </div>
            </div>
        </div>

        <div class="row mt-3" style="display: flex;display: none;">
            <div class="col-md-6" style="width: 50%;display: none;">
                <h5>Airport Parking</h5>
                <h5>00443940825851, dallen.airportparking@gmail.com </h5>
            </div>
            <div class="col-md-6 text-end" style="width: 50%;display: none;">
                <div class="row g-0">
                    <h5>Invoice No: <span id="invoiceNo"></span></h5>
                    <hr style="width: 62%;     margin-top: 10px;">
                    <h5>Invoice Date: <span id="dateFrom"></span></h5>
                    <h5 id="dateTo">31-07-2024</h5>
                    <br>
                </div>
            </div>
        </div><br>
        <div class="table-responsive border webtable mt-3" style="max-width: 100%;margin-top: 40%;">
            <table id="webbookingTable" class="table table-striped" style="width: 100%;">
                <thead>
                    <tr>
                    <th><?= ($isSupplier)? 'Source': 'Product';?></th>
                    <th>QTY</th>
                    <th>Gross Amount</th>
                    <th>Net Amount</th>
                    <?= ($isSupplier)? '': '<th>Fee</th>';?>
                    </tr>
                </thead>
                <tbody>
                    <?php if($html):
                        echo $html;
                    else:?>
                    <tr>
                        <td><?= $result->name ?></td>
                        <td><?= $result->qty ?></td>
                        <td><?= $saleTotal ?></td>
                        <td><?= $netAmount?></td>
                        <?= ($isSupplier)? '': '<td>'.$totFee.'</td>';?>
                    </tr>
                    <?php endif;?>
                </tbody>
                <tfoot>
                    
                    <!-- <tr hidden>
                        <td colspan="3"></td>
                        <th>Booking Amount:</th>
                        <th class="price"><?= $saleTotal?></th>
                    </tr> -->
                    <?php if ($operatorTotal > 0 && $isSupplier):?>
                    <tr>
                        <td colspan="<?= ($isSupplier)? "":"2" ?>"></td>
                        <th colspan="3" style="text-align: right;">Supplier Commission:</th>
                        <th class="price">(<?= $operatorTotal?>)</th>
                    </tr>
                    <tr>
                        <td colspan="<?= ($isSupplier)? '3':'4' ?>"></td>
                        <th style="text-align: right;">Total:</th>
                        <th class="price"><?= $subTotal?></th>
                    </tr>
                    <?php endif;?>
                    <tr>
                        <th colspan="6" style="border-bottom: 2px solid #EBE9F1;text-align: center;">Admin Income Calculation</th>
                    </tr>
                    <tr>
                        <th colspan="<?= ($isSupplier)? '3':'4' ?>">10% of Gross website Income:</th>
                        <th class="price"><?= $grossIncom?></th>
                        <td></td>
                    </tr>
                    <?php if ($totFee > 0):?>
                    <tr>
                        <th colspan="<?= ($isSupplier)? '3':'4' ?>">Booking Fee:</th>
                        <th class="price"><?= $totFee?></th>
                        <td></td>
                    </tr>
                    <?php endif; if (!empty($inputLabels)):
                        foreach ($inputLabels as $key => $l) {
                            if ($l) {
                            echo'<tr>
                                <th colspan="'.($isSupplier)? '3':'4'.'">'.$l.':</th>
                                <th class="price">'.$inputValues[$key].'</th>
                                <td></td>
                            </tr>';
                            }
                        }
                    endif; ?>
                    <tr>
                        <th class="total" colspan="<?= ($isSupplier)? '3':'4' ?>">Admin Income:</th>
                        <th class="total price"><?= $totalPayable?></th>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
