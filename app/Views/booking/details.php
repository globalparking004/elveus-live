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

<section id="outline-button">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Change Booking Status & Price</h4>
        </div>
        <div class="card-body">

         <div class="row">   
            <div class="col-md-6" style="display: none;">
                <div class="mb-1"> 
                    <label>Parking Status</label>
                    <select class="select2" id="status">
                        <option value="0">Pending</option>
                        <option selected value="1">Completed</option>                                
                        <option value="2">Cancelled</option>
                        <option value="3">No Show</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-1"> 
                    <label>Amount Charged</label>
                    <input type="text" class="form-control" name="price" id="price" value="<?= $booking->price; ?>">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-1"> 
                    <label>Transaction Id</label>
                    <input type="text" class="form-control" name="receipt_number" id="receipt_number" value="" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-1"> 
                    <a href="javascript:void(0);" onclick="updateParkingStatus('<?= $booking->id; ?>')" class="btn btn-primary waves-effect waves-float waves-light">Complete Booking</a>
                </div>
            </div>      
         </div>
            
        </div>
      </div>
    </div>
  </div>
</section>

<section id="outline-button">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Booking Details</h4>
        </div>
        <div class="card-body">
          
          <div class="row">
                        <div class="col-md-3 mb-1">
                            <strong>Operator</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                <?php 
                                    $operator=get_operator($booking->operator_id); 
                                    if($operator)
                                    {
                                        echo $operator->description;
                                    }
                                ?>                            
                            </span>
                        </div>
                                                <div class="col-md-3">
                                <strong>Location</strong>
                            </div>
                            <div class="col-md-3 mb-1">
                            <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                United Kingdom                            
                            </span>
                            </div>
                                            </div>
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <strong>Reference</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <b><?= $booking->reference; ?></b>
                        </div>
                                                <div class="col-md-3">
                            <strong>Customer</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                           <?= $booking->firstName ." ". $booking->surname ;?>                            
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <strong>Booked On</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <?= date("d-M-Y H:i:s", strtotime($booking->created_at)); ?>                        
                        </div>
                        <div class="col-md-3 mb-1">
                            <strong>Car Park</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <span class="label label-info text-uppercase badge badge-glow bg-info">
                            <?php 
                                $airports=get_airports();
                                echo $airports[$booking->airport]; 
                            ?>         
                            </span>                  
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <strong>Arrival</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <?= date("d-M-Y H:i:s", strtotime($booking->depart_at)); ?>                             
                        </div>
                        <div class="col-md-3 mb-1">
                            <strong>Departure</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <?= date("d-M-Y H:i:s", strtotime($booking->return_at)); ?>                        
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <strong>Vehicle Reg</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <?= $booking->carReg; ?>                       
                        </div>

                        <div class="col-md-3 mb-1">
                            <strong>Telephone Number</strong>
                        </div>
                        <div class="col-md-3 mb-1">
                            <?= $booking->contactNumber ?>                        
                        </div>
                    </div>




                </div>
          
        </div>
      </div>
    </div>
  </div>
</section>

            </div>
            <?= $this->endSection(); ?>
            <?= $this->section("javascript"); ?>
            <script type="text/javascript">
                var jsstatus='';
                $(document).ready(function() {
                    jsstatus=true;
                    //$("#status").val("<?= $booking->status ?>");
                    //$('#status').trigger('change');
                });
                function updateParkingStatus(id)
                {     
                       var status=$("#status").val();
                       var price=$("#price").val();
                       var receipt_number=$("#receipt_number").val();
                        $.ajax({
                          url: "<?= base_url('bookings/update_status'); ?>",
                          type: 'GET',
                          dataType: 'json',
                          data:"id="+id+"&status="+status+"&price="+price+"&receipt_number="+receipt_number,
                          beforeSend: function() {
                           
                          },
                          success: function (res) {
                            
                            if(res.status){
                                toastr['success'](res.message, 'Success!', {
                                  closeButton: true,
                                  tapToDismiss: true,
                                  progressBar: true
                                });
                              }else{           
                                  toastr['error'](res.message, 'Error!', {
                                    closeButton: true,
                                    tapToDismiss: true,
                                    progressBar: true,
                                  });                                
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