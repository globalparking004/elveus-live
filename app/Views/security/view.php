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
            <h2 class="content-header-title float-start mb-0"><?= $page_title; ?></h2>
          </div>
        </div>
      </div>
      <!-- <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#add-xlarge">
          <i data-feather='plus'></i> Add Security Guard
        </button>
      </div> -->
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
                      <th>Price</th>
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

<!-- Modal Add/Edit Security Guard -->
<div class="modal fade text-start" id="add-xlarge" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form class="form-crud" id="form-crud" action="<?= base_url('security/save'); ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="id" value="" />
        <div class="modal-header">
          <h4 class="modal-title" id="form-crud-title">Add Security Guard</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="price">Price</label>
                <input type="text" id="price" class="form-control" placeholder="Enter Price" name="price" />
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
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

// Initialize DataTable
var table = $('#view-datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= base_url('security/get'); ?>",
    language: { "processing": "<i class='fas fa-spinner fa-spin'></i> Loading Data..." }
});

// Form submit
$(".form-crud").validate({
    rules: { price: { required: true, number: true } },
    messages: { price: { required: "Please enter price", number: "Price must be a number" } },
    submitHandler: function(form){
        $.post($(form).attr('action'), $(form).serialize(), function(res){
            if(res.status){
                toastr.success(res.message);
                table.ajax.reload(null,false);
                $('#add-xlarge').modal('hide');
                form.reset();
            } else {
                toastr.error(res.message);
            }
        }, 'json');
        return false;
    }
});

// Edit
function edit_data(id){
    $.get("<?= base_url('security/get_record'); ?>?id="+id, function(res){
        if(res.status){
            $("#id").val(id);
            $("#price").val(res.data.price);
            $("#status").val(res.data.status);
            $("#form-crud").attr('action', "<?= base_url('security/update'); ?>");
            $("#form-crud-title").text("Edit Security Guard");
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
      customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: "<?= base_url('security/delete_record'); ?>",
          type: 'GET',
          dataType: 'json',
          data: {id: id},
          success: function (data) {
            if(data.status){ toastr.success(data.message); table.ajax.reload(); }
            else { toastr.error(data.message); }
          },
          error: function(){ toastr.error('Something went wrong!'); }
        });
      }
    });
}

// Reset form on modal close
$("#add-xlarge").on("hidden.bs.modal", function(){
    $("#form-crud")[0].reset();
    $("#id").val('');
    $("#form-crud-title").text("Add Security Guard");
    $("#form-crud").attr("action", "<?= base_url('security/save'); ?>");
});

</script>
<?= $this->endSection(); ?>
