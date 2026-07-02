<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
	<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<style type="text/css">
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
    #review{
        font-size: large;
        line-height: 2rem;
        text-align: justify;
    }
    .review-card {
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .review-card span{
        font-size: large;
        font-weight: bold;
    }
    .progress {
        height: 10px;
        margin-bottom: 5px;
    }
    .star-rating {
        font-size: 0.9rem;
        color: #ffc107;
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
                     
                        <div class="col-md-6 col-6">
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

                        <div class="col-md-6 col-6">
                            <div class="mb-1">
                                <label class="form-label">Rating</label>
                                <select class="form-select select2" id="rating" name="rating">
                                    <option value="*">All</option> 
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Average</option>
                                    <option value="2">2 - Poor</option>
                                    <option value="1">1 - Terrible</option>
                                 </select>
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
                                <ul class="nav nav-tabs mt-1">
                                  <li class="active"><a data-toggle="tab" href="#dashboard" class="atab">Dashbard</a></li>
                                  <li ><a data-toggle="tab" href="#reviews" class="atab">List</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="dashboard" class="tab-pane active">
                                        <h2 class="text-center mb-4">Airport Reviews</h2>
                                        <div class="row" id="airport-cards-container">
                                            <!-- Cards will be generated here -->
                                            <?php if ($review_stats) {
                                                foreach ($review_stats as $key => $r) {?>
                                            <div class="col-md-4">
                                                <div class="card review-card">
                                                    <div class="card-header bg-primary">
                                                        <h3 class="card-title mb-0 text-white"><?= $r->airport ?> Airport</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div>
                                                                <span class="star-rating">
                                                                    <?= get_review_stars($r->average_rating) ?>
                                                                </span>
                                                                <span class="ms-2"><?= $r->total_reviews ?> reviews</span>
                                                            </div>
                                                            <!-- <span class="badge bg-${avgRating >= 4 ? 'success' : avgRating >= 3 ? 'warning' : 'danger'}">
                                                                ${avgRating}/5
                                                            </span> -->
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>5 stars</span>
                                                                <span><?= $r->five_stars ?> (<?= round(($r->five_stars/$r->total_reviews)*100,2) ?>%)</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-success" role="progressbar" 
                                                                     style="width: <?= ($r->five_stars/$r->total_reviews)*100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>4 stars</span>
                                                                <span><?= $r->four_stars ?> (<?= round(($r->four_stars/$r->total_reviews)*100,2) ?>%)</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-info" role="progressbar" 
                                                                     style="width: <?= ($r->four_stars/$r->total_reviews)*100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>3 stars</span>
                                                                <span><?= $r->three_stars ?> (<?= round(($r->three_stars/$r->total_reviews)*100,2) ?>%)</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-warning" role="progressbar" 
                                                                     style="width: <?= ($r->three_stars/$r->total_reviews)*100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>2 stars</span>
                                                                <span><?= $r->two_stars ?> (<?= round(($r->two_stars/$r->total_reviews)*100,2) ?>%)</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-orange" role="progressbar" 
                                                                     style="width: <?= ($r->two_stars/$r->total_reviews)*100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>1 star</span>
                                                                <span><?= $r->one_star ?> (<?= round(($r->one_star/$r->total_reviews)*100,2) ?>%)</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                                     style="width: <?= ($r->one_star/$r->total_reviews)*100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }}?>
                                        </div>
                                    </div>
                                    <div id="reviews" class="tab-pane ">
                                        <div class="card-datatable" style="margin: 10px;">
                                            <table class="datatables-ajax table table-responsive" id="view-datatable">
                                              <thead>
                                              <tr>
                                                  <th>Referance</th>
                                                  <th>Airport</th>
                                                  <th>Customer</th>
                                                  <th>Rating</th>
                                                  <th>Review</th>
                                                  <th>Status</th>
                                                  <th style="width: 12%">Date</th>
                                                  <th style="width: 10%">Action</th>
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
    <div class="modal fade text-start" id="view-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="form-crud-title">View Review</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">    
                  <div class="col-md-12 col-12">
                    <div class="mb-1">
                        <p id="review"></p>
                    </div>
                  </div>
                                                
                </div>
            </div>
            <div class="modal-footer">                      
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button> -->
            </div>
        </div>
      </div>
    </div>

<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">
    $('.atab').on('click', function() {
        let id = $(this).attr('href');
        $('.nav-tabs').find('li').removeClass('active');
        $('.tab-content').find('div').removeClass('active');
        $(this).parent().addClass('active');
        $(id).addClass('active');
    });
    var table;
    table=$('#view-datatable').DataTable({
        dom: '<l>Bfrtip',
        processing: true,
        serverSide: true,
        // select: true,
        ajax: {
            url: "<?= url_to('reviews/get');?> ",
            type: 'GET', 
            data: function(d) 
            {
                d.airport=$("#airport").val();
                d.rating=$("#rating").val();
            },
            complete: function(data){
                feather.replace();    
            }
        },
        language: { 
            "processing": "<i class=\"fas fa-spinner fa-spin\"></i><span>  Loading Data...</span>"
        },
        "lengthMenu": [
                [20, 50, 100, 200, 300, 400, 500],
                [20, 50, 100, 200, 300, 400, 500],
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

    function view_review(desc) {
        $('#view-modal').modal('show');
        $('#review').text(desc);
    }

    function publish_review(id,status) 
    {
        $.ajax({
            url: "<?= base_url('reviews/publish'); ?>",
            type: 'GET',
            dataType: 'json',
            data: "id=" + encodeURIComponent(id)+'&status='+status,
            success: function(data) {
                // console.log(data.details);
                if(data.status){
                    toastr['success'](data.message, 'Success!', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
                    location.reload();
                }else{           
                    toastr['error'](data.message, 'Error!', {
                        closeButton: true,
                        tapToDismiss: true,
                        progressBar: true,
                    });       
                } 
                                
            },
            error: function(xhr) {

            }
        }); 
    }

    function delete_review(id) 
    {
        if (confirm("Are you sure you want to delete this review? This action cannot be undone.")) {
            $.ajax({
                url: "<?= base_url('reviews/delete'); ?>",
                type: 'GET',
                dataType: 'json',
                data: "id=" + encodeURIComponent(id),
                success: function(data) {
                    // console.log(data.details);
                    if(data.status){
                        toastr['success'](data.message, 'Success!', {
                          closeButton: true,
                          tapToDismiss: true,
                          progressBar: true
                        });
                        location.reload();
                    }else{           
                        toastr['error'](data.message, 'Error!', {
                            closeButton: true,
                            tapToDismiss: true,
                            progressBar: true,
                        });       
                    } 
                                    
                },
                error: function(xhr) {
                    toastr.error('Something went wrong. Please try again later.', 'Error', {
                      closeButton: true,
                      tapToDismiss: true,
                      progressBar: true
                    });
      
                }
            }); 
        }else {
            toastr.info('Deletion canceled.', 'Notice', {
              closeButton: true,
              tapToDismiss: true,
              progressBar: true
            });
        }
    }

</script>
<?= $this->endSection(); ?>