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
          <i data-feather='plus'></i> Add Color
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
                      <th>Color Name</th>
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

<!-- Modal Add/Edit Company -->
<div class="modal fade text-start" id="add-xlarge" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <form class="form-crud" id="form-crud" action="<?= base_url('vehicles/colors/save'); ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="id" value="" />

        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title">Add Color</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-1">
            <label class="form-label" for="color_name">Color Name</label>
            <input type="text" id="color_name" class="form-control"
                   placeholder="Enter Color Name" name="color_name" />
          </div>

          <div class="mb-1">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
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

// DataTable
var table = $('#view-datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= base_url('vehicles/colors/get'); ?>",
    language: { "processing": "<i class='fas fa-spinner fa-spin'></i> Loading Data..." }
});

// Validation & Submit
$(".form-crud").validate({
    rules: {
        color_name: { required: true},
        status: { required: true }
    },
    messages: {
        color_name: { required: "Please enter color name" },
        status: { required: "Please select status" }
    },
    submitHandler: function(form){
        $.post($(form).attr('action'), $(form).serialize(), function(res){

            if(res.status){
                toastr.success(res.message); // success toast
                table.ajax.reload(null,false); 
                $('#add-xlarge').modal('hide');
                form.reset();
            }
            else if(res.errors){
                // ✅ Server validation errors ko show karo
                $.each(res.errors, function(key, msg){
                    toastr.error(msg);
                });
            }
            else{
                toastr.error(res.message ?? 'Something went wrong!');
            }

        }, 'json');
        return false;
    }
});


// Edit
function edit_data(id){
    $.get("<?= base_url('vehicles/colors/get_record'); ?>?id="+id, function(res){
        if(res.status){
            $("#id").val(id);
            $("#color_name").val(res.data.color_name);
            $("#status").val(res.data.status);
            $("#form-crud").attr('action', "<?= base_url('vehicles/colors/update'); ?>");
            $("#form-crud-title").text("Edit Color");
            $('#add-xlarge').modal('show');
        }
    }, 'json');
}

// Delete
function delete_data(id) {
    Swal.fire({
      title: 'Are you sure?',
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
          url: "<?= base_url('vehicles/colors/delete_record'); ?>",
          type: 'GET',
          dataType: 'json',
          data: {id: id},
          success: function (data) {
            if(data.status){
                toastr.success(data.message);
                table.ajax.reload();
            } else {
                toastr.error(data.message);
            }
          },
          error: function(){
              toastr.error('Something went wrong!');
          }
        });
      }
    });
}

// Reset modal
$("#add-xlarge").on("hidden.bs.modal", function(){
    $("#form-crud")[0].reset();
    $("#id").val('');
    $("#form-crud-title").text("Add Color");
    $("#form-crud").attr("action", "<?= base_url('vehicles/colors/save'); ?>");
});

</script>
<?= $this->endSection(); ?>
