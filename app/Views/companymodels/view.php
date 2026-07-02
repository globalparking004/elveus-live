<?= $this->extend("layouts/base"); ?>

<?= $this->section("title"); ?>
<?= $page_title; ?>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
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
      <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#add-xlarge">
          <i data-feather='plus'></i>
          <span>Add Model</span>
        </button>
      </div>
    </div>
    <div class="content-body">
      <section id="ajax-datatable">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-datatable" style="margin: 10px;">
                <table class="datatables-ajax table table-responsive" id="view-datatable">
                  <thead>
                    <tr>
                      <th>Created At</th>
                      <th>Make</th>
                      <th>Model</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- Modal Add/Edit Vehicle -->
<div class="modal fade text-start" id="add-xlarge" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form class="form-crud" id="form-crud" action="<?= base_url('vehicles/companymodels/save'); ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="id" value="" />
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title">Add Model</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Make -->
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="make_id">Make</label>
                <select class="form-select select2" id="make_id" name="make_id">
                  <option value="">Select Make</option>
                  <?php foreach($makes as $make){ ?>
                    <option value="<?= $make['id']; ?>"><?= $make['name']; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="name">Car Quantity</label>
                <input type="text" id="name" class="form-control" placeholder="Model Name" name="name" />
              </div>
            </div>
            <div class="col-md-6 col-12">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-secondary">Reset</button>
          <button type="submit" id="btnsubmit" class="btn btn-primary">Continue</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section("javascript"); ?>
<script type="text/javascript">
  $('.select2').select2();

// Load models when make changes
$('#make_id').on('change', function(){
    var make_id = $(this).val();
    $('#model_id').html('<option value="">Loading...</option>');
    if(make_id){
        $.get("<?= base_url('vehicles/companymodels/get_models'); ?>?make_id=" + make_id, function(res){
            var options = '<option value="">Select Model</option>';
            res.forEach(function(model){
                options += '<option value="'+model.id+'">'+model.name+'</option>';
            });
            $('#model_id').html(options);
        }, 'json');
    } else {
        $('#model_id').html('<option value="">Select Model</option>');
    }
});

// Initialize DataTable
var table = $('#view-datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= base_url('vehicles/companymodels/get'); ?>",
    language: { "processing": "<i class='fas fa-spinner fa-spin'></i> Loading Data..." },
    columnDefs: [{ "targets": 0, "orderable": false }]
});

// Form validation and submit
$(".form-crud").validate({
    rules: {
        make_id: { required: true },
        name: { required: true }
    },
    messages: {
        make_id: { required: "Please select make" },
        name: { required: "Please enter model name" }
    },
    submitHandler: function(form){
        $.post($(form).attr('action'), $(form).serialize(), function(res){
            if(res.status){
                toastr.success(res.message);
                table.ajax.reload(null, false); // reload datatable
                $('#add-xlarge').modal('hide');
                form.reset();
            } else {
                toastr.error(res.message);
            }
        }, 'json');
        return false;
    }
});

// Edit record
function edit_data(id){
    $.get("<?= base_url('vehicles/companymodels/get_record'); ?>?id="+id, function(res){
        if(res.status){

            $("#id").val(id);

            // 1: Set Make
            $("#make_id").val(res.data.make_id).trigger('change');

           
            $("#name").val(res.data.name);
            $("#status").val(res.data.status);

            $("#form-crud").attr('action', "<?= base_url('vehicles/companymodels/update'); ?>");
            $("#form-crud-title").text("Edit Model");

            $('#add-xlarge').modal('show');
        }
    }, 'json');
}


// Delete record
function delete_data(id) {
    Swal.fire({
      title: 'Are you sure you want to delete?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-outline-danger ms-1'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: "<?= base_url('vehicles/companymodels/delete_record'); ?>", // Vehicles ka URL
          type: 'GET',
          dataType: 'json',
          data: "id=" + encodeURIComponent(id),
          beforeSend: function () {
            // Optional: loader
          },
          success: function (data) {
            if (data.status) {
              toastr['success'](data.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              table.draw(); // DataTable reload
            } else {
              toastr['error'](data.message, 'Error!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true,
              });
            }
          },
          error: function (xhr) {
            toastr['error']('Something went wrong!', 'Error!');
          },
          complete: function () {
            // Optional
          }
        });
      }
    });
}


// Reset form on modal close
$("#add-xlarge").on("hidden.bs.modal", function(){
    $("#form-crud")[0].reset();
    $("#id").val('');
    $("#form-crud-title").text("Add Vehicle");
    $("#form-crud").attr("action", "<?= base_url('vehicles/companymodels/save'); ?>");
});

</script>
<?= $this->endSection(); ?>
