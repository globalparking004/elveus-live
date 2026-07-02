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

      <div class="card" style="display: none;">
        <h5 class="card-header">Search Filter</h5>
        <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
          <div class="col-md-4 user_role"></div>
          <div class="col-md-4 user_plan"></div>
          <div class="col-md-4 user_status"></div>
        </div>
      </div>



      <!-- Vertical Wizard -->
      <form action="<?= base_url('products/update'); ?>" method="POST" id="frmsubmit">
        <?= csrf_field() ?>
        <section class="vertical-wizard">

          <div class="bs-stepper vertical vertical-wizard-example">
            <div class="bs-stepper-header">
              <div class="step" data-target="#contact-information-vertical" role="tab" id="contact-information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">1</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Contact Information</span>
                  </span>
                </button>
              </div>
              <div class="step" data-target="#key-information-vertical" role="tab" id="key-information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">2</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Key Information</span>
                  </span>
                </button>
              </div>
              <div class="step" data-target="#availability-information-vertical" role="tab" id="availability-information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">3</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Availability Information</span>
                  </span>
                </button>
              </div>
              <div class="step" data-target="#information-vertical" role="tab" id="information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">4</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Information</span>
                  </span>
                </button>
              </div>

              <div class="step" data-target="#trusted-score-vertical" role="tab" id="trusted-score-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">5</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Trusted Score Elements</span>
                  </span>
                </button>
              </div>

              <div class="step" data-target="#email-confirmation-vertical" role="tab" id="email-confirmation-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">6</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Email Confirmation</span>
                  </span>
                </button>
              </div>

              <div class="step" data-target="#capacity-information-vertical" role="tab" id="capacity-information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">7</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Capacity Information</span>
                  </span>
                </button>
              </div>

              <div class="step" data-target="#map-information-vertical" role="tab" id="map-information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">8</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Map & Photo's</span>
                  </span>
                </button>
              </div>

              <div class="step" data-target="#addons-information-vertical" role="tab" id="addons-information-vertical-trigger">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-box">9</span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Addons</span>
                  </span>
                </button>
              </div>

            </div>


            <div class="bs-stepper-content">
              <div id="contact-information-vertical" class="content" role="tabpanel" aria-labelledby="contact-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Contact Information</h5>
                </div>
                <input type="hidden" name="id" name="id" value="<?= $product['id']; ?>" />
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="product_code">Product Code</label>
                    <input type="text" value="<?= $product['product_code']; ?>" id="product_code" name="product_code" class="form-control alphanumeric-input" placeholder="Product Code" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" value="<?= $product['name']; ?>" id="name" name="name" class="form-control" placeholder="Name" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="name_ar">Arabic Name</label>
                    <input type="text" value="<?= $product['name_ar']; ?>" id="name_ar" name="name_ar" class="form-control" placeholder="Name" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="name">Telephone</label>
                    <input type="text" value="<?= $product['telephone']; ?>" id="telephone" name="telephone" class="form-control number-input" placeholder="Telephone" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-8">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" value="<?= $product['address']; ?>" id="address" name="address" class="form-control" placeholder="Address" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="postcode">Post Code</label>
                    <input type="text" value="<?= $product['postcode']; ?>" id="postcode" name="postcode" class="form-control" placeholder="Post Code" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="parent">Airport/Suppliers</label>
                     <select class="select2" id="parent" name="parent">
                      <?php $airports = get_airports();
                      echo "<option value='*'>NONE</option>";
                      // echo "<option value='*'>select Airport</option>";

                      foreach ($airports as $code => $name) {
                        if ($code == $product['parent']) {
                          echo "<option selected value='$code'>$name</option>";
                        } else {
                          echo "<option value='$code'>$name</option>";
                        }
                      }
                      echo "<option value='*'>----- select Supplier -----</option>";
                      foreach ($suppliers as $supplier) {

                        $s_code=$supplier['code'];
                        $s_name=$supplier['name'];
                        if ($supplier['code'] == $product['parent']) {
                  
                          echo "<option selected value='$s_code'>Supplier $s_name</option>";
                        } else {
                          echo "<option value='$s_code'>Supplier $s_name</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="airport">API-Airport</label>
                     <select class="select2" id="airport" name="airport">
                      <?php $airports = get_airports();
                      echo "<option value='*'>NONE</option>";

                      foreach ($airports as $code => $name) {
                        if ($code == $product['airport']) {
                          echo "<option selected value='$code'>$name</option>";
                        } else {
                          echo "<option value='$code'>$name</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 form-password-toggle col-md-2">
                    <label class="form-label" for="latitude">Latitude</label>
                    <input type="text" value="<?= $product['latitude']; ?>" id="latitude" name="latitude" class="form-control number-input" placeholder="Latitude" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-2">
                    <label class="form-label" for="longitude">Longitude</label>
                    <input type="text" value="<?= $product['longitude']; ?>" id="longitude" name="longitude" class="form-control number-input" placeholder="Longitude" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="operator_id">Operator</label>
                    <select class="select2" id="operator_id" name="operator_id">
                      <?php
                      echo "<option value='*'>NONE</option>";
                      for ($i = 0; $i < sizeof($operators); $i++) {
                        if ($product['operator_id'] == $operators[$i]['id']) {
                          echo "<option selected value='" . $operators[$i]['id'] . "'>" . $operators[$i]['description'] . "</option>";
                        } else {
                          echo "<option value='" . $operators[$i]['id'] . "'>" . $operators[$i]['description'] . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="operator_id">Operator visibility</label>
                    <select class="select2" id="operator_id_show" name="operator_id_show">
                      <?php
                      if ($product['operator_id_show'] == 0) {
                        echo "<option selected value='0'>NO</option>";
                        echo "<option  value='1'>YES</option>";

                      }else{
                        echo "<option selected value='1'>YES</option>";
                        echo "<option  value='0'>NO</option>";

                      }

                   

                      
                      ?>
                    </select>
                  </div>







                </div>

                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-outline-secondary btn-prev" disabled>
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                </div>
              </div>

              <div id="key-information-vertical" class="content" role="tabpanel" aria-labelledby="key-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Key Information</h5>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="logo">Logo</label>
                    <input type="file" id="logo" name="logo" class="form-control" placeholder="Logo" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="logo1">New Website Logo</label>
                    <input type="file" id="logo1" name="logo1" class="form-control"/>
                    <?= ($product['logo1']) ? '<img width="50" src="'.BASEURL.'logos/products/'. $product['logo1'].'">':''?>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="distance_miles">Distance to Airport (miles)</label>
                    <input type="text" value="<?= $product['distance_miles']; ?>" id="distance_miles" name="distance_miles" class="form-control" placeholder="Distance to Airport (miles)" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="transfer_time">Transfer Time</label>
                    <input type="text" value="<?= $product['transfer_time']; ?>" id="transfer_time" name="transfer_time" class="form-control number-input" placeholder="Transfer Time" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="customize1">Customize 1</label>
                    <input type="text" value="<?= $product['customize1']; ?>" id="customize1" name="customize1" class="form-control" placeholder="Customize1" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="customize1">Customize 2</label>
                    <input type="text" value="<?= $product['customize2']; ?>" id="customize2" name="customize2" class="form-control" placeholder="Customize2" />
                  </div>
                </div>
                <?php
                $is_none_amendable = "";
                if ($product['is_none_amendable'] == "1") {
                  $is_none_amendable = "checked";
                }
                $meet_and_greet = "";
                if ($product['meet_and_greet'] == "1") {
                  $meet_and_greet = "checked";
                }
                $on_airport = "";
                if ($product['on_airport'] == "1") {
                  $on_airport = "checked";
                }
                $park_mark = "";
                if ($product['park_mark'] == "1") {
                  $park_mark = "checked";
                }

                ?>
                <div class="row">
                  <div class="mb-1 col-md-4" style="display: none;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="is_none_amendable" name="is_none_amendable" value="checked" <?= $is_none_amendable; ?> />
                      <label class="form-check-label" for="is_none_amendable">Is None Amendable</label>
                    </div>
                  </div>
                  <div class="mb-1 col-md-4">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="meet_and_greet" name="product_type" value="Meet & Greet" <?= ($product['product_type'] == 'Meet & Greet')? 'checked="checked"':''; ?> />
                      <label class="form-check-label" for="meet_and_greet">Meet and Greet</label>
                    </div>
                  </div>

                  <div class="mb-1 col-md-2" style="display:none;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="on_airport" name="product_type" value="On Airport" <?= ($product['product_type'] == 'On Airport')? 'checked="checked"':''; ?> />
                      <label class="form-check-label" for="on_airport">On Airport</label>
                    </div>
                  </div>

                  <div class="mb-1 col-md-3">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="park_mark" name="product_type" value="Park & Ride" <?= ($product['product_type'] == 'Park & Ride')? 'checked="checked"':''; ?> />
                      <label class="form-check-label" for="park_mark">Park and Ride</label>
                    </div>
                  </div>

                  <div class="mb-1 col-md-2">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input product_type" type="radio" name="product_type" value="Station" <?= ($product['product_type'] == 'Station')? 'checked="checked"':''; ?>/>
                      <label class="form-check-label" for="station">Station</label>
                    </div>
                  </div>

                  <div class="mb-1 col-md-3">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input product_type" type="radio" name="product_type" value="Park & Stroll" <?= ($product['product_type'] == 'Park & Stroll')? 'checked="checked"':''; ?>/>
                      <label class="form-check-label" for="park_and_stroll">Park and Stroll</label>
                    </div>
                  </div>

                </div>

                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                </div>
              </div>

              <div id="availability-information-vertical" class="content" role="tabpanel" aria-labelledby="availability-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Availability Information</h5>
                  <?php $shifts = get_shift_time(); ?>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="notice_period">Notice Period (Hours)</label>

                    <input type="text" id="notice_period" name="notice_period" class="form-control number-input" placeholder="Notice Period (Hours)" value="<?=$product['notice_period'];?>"/>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="opening_time">Opening Time</label>
                    <select class="select2" id="opening_time" name="opening_time">

                      <?php
                      foreach ($shifts as $code => $name) {
                        if ($product['opening_time'] == $code) {
                          echo "<option selected value='$code'>$name</option>";
                        } else {
                          echo "<option value='$code'>$name</option>";
                        }
                      }
                      ?>

                    </select>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="closing_time">Closing Time</label>
                    <select class="select2" id="closing_time" name="closing_time">
                      <?php
                      foreach ($shifts as $code => $name) {
                        if ($product['closing_time'] == $code) {
                          echo "<option selected value='$code'>$name</option>";
                        } else {
                          echo "<option value='$code'>$name</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="commission">Commission</label>
                    <input type="text" value="<?= $product['commission']; ?>" id="commission" name="commission" class="form-control number-input" placeholder="Commission" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="exclusive_to_website_id">Exclusively For Website</label>
                    <select class="select2" id="exclusive_to_website_id" name="exclusive_to_website_id">
                      <option value="*">ALL</option>
                      <?php foreach ($websites as $website) {

                        if($product['exclusive_to_website_id']==$website->id){

                          echo "<option selected value='$website->id'>$website->web_name</option>";

                        }else{
                          echo "<option value='$website->id'>$website->web_name</option>";

                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="linked_product_code">Link Pricing To Product Code</label>
                    <input type="text" value="<?= $product['linked_product_code']; ?>" id="linked_product_code" name="linked_product_code" class="form-control" placeholder="Link Pricing To Product Code" />
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="linked_price">Adjust Linked Prices By +/-</label>
                    <input type="text" value="<?= $product['linked_price']; ?>" id="linked_price" name="linked_price" class="form-control price-input" placeholder="Adjust Linked Prices" value="0.00" />
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                </div>
              </div>

              <div id="information-vertical" class="content" role="tabpanel" aria-labelledby="information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Information</h5>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="directions">Directions</label>
                    <textarea class="form-control" id="directions" name="directions" rows="3"><?= $product['directions']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label">Directions Arabic</label>
                    <textarea class="form-control" id="directions_ar" name="directions_ar" rows="3"><?= $product['directions_ar']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="introduction">Introduction</label>
                    <textarea class="form-control" id="introduction" name="introduction" rows="3"><?= $product['introduction']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label">Introduction Arabic</label>
                    <textarea class="form-control" id="introduction" name="introduction_ar" rows="3"><?= $product['introduction_ar']; ?></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="information">Information</label>
                    <textarea class="form-control" id="information" name="information" rows="3"><?= $product['information']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label">Information Arabic</label>
                    <textarea class="form-control" id="information_ar" name="information_ar" rows="3"><?= $product['information_ar']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="security_measures">Security Measures</label>
                    <textarea class="form-control" id="security_measures" name="security_measures" rows="3"><?= $product['security_measures']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="security_measures_ar">Security Measures Arabic</label>
                    <textarea class="form-control" id="security_measures_ar" name="security_measures_ar" rows="3"><?= $product['security_measures_ar']; ?></textarea>
                  </div>
                </div>


                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="arrival_procedures">Arrival Procedures</label>
                    <textarea class="form-control" id="arrival_procedures" name="arrival_procedures" rows="3"><?= $product['arrival_procedures']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="arrival_procedures_ar">Arrival Procedures Arabic</label>
                    <textarea class="form-control" id="arrival_procedures_ar" name="arrival_procedures_ar" rows="3"><?= $product['arrival_procedures_ar']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="departure_procedures">Departure Procedures</label>
                    <textarea class="form-control" id="departure_procedures" name="departure_procedures" rows="3"><?= $product['departure_procedures']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="departure_procedures_ar">Departure Procedures Arabic</label>
                    <textarea class="form-control" id="departure_procedures_ar" name="departure_procedures_ar" rows="3"><?= $product['departure_procedures_ar']; ?></textarea>
                  </div>
                </div>


                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="disabled_facilities">Disabled Facilities</label>
                    <textarea class="form-control" id="disabled_facilities" name="disabled_facilities" rows="3"><?= $product['disabled_facilities']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="disabled_facilities_ar">Disabled Facilities</label>
                    <textarea class="form-control" id="disabled_facilities_ar" name="disabled_facilities_ar" rows="3"><?= $product['disabled_facilities_ar']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="transfers">Transfers</label>
                    <textarea class="form-control" id="transfers" name="transfers" rows="3"><?= $product['transfers']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="transfers_ar">Transfers Arabic</label>
                    <textarea class="form-control" id="transfers_ar" name="transfers_ar" rows="3"><?= $product['transfers_ar']; ?></textarea>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                </div>
              </div>

              <div id="trusted-score-vertical" class="content" role="tabpanel" aria-labelledby="trusted-score-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Trusted Score Elements</h5>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_price">Price Score</label>
                    <input type="text" id="score_price" name="score_price" class="form-control number-input" placeholder="Price Score" value="<?= $product['score_price']; ?>" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_accessibility">Accessibility Score</label>
                    <input type="text" id="score_accessibility" name="score_accessibility" class="form-control number-input" placeholder="Accessibility Score" value="<?= $product['score_accessibility']; ?>" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_efficiency">Efficiency Score</label>
                    <input type="text" id="score_efficiency" name="score_efficiency" class="form-control number-input" placeholder="Efficiency Score" value="<?= $product['score_efficiency']; ?>" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_security">Security Score</label>
                    <input type="text" id="score_security" name="score_security" class="form-control number-input" placeholder="Accessibility Score" value="<?= $product['score_security']; ?>" />
                  </div>
                </div>
                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                </div>
              </div>

              <div id="email-confirmation-vertical" class="content" role="tabpanel" aria-labelledby="email-confirmation-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Email Confirmation</h5>
                  <button type="button" class="btn btn-primary btn-emailConfig">
                    <i data-feather="plus" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Add More</span>
                  </button>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="useful_information">Useful Information</label>
                    <textarea class="form-control" id="useful_information" name="useful_information" rows="3"><?= $product['useful_information']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="driver_contact">Driver Contact Number</label>
                    <input type="text" name="driver_contact" id="driver_contact" class="form-control" value="<?= $product['driver_contact']; ?>">
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="parking_facility_contact">Parking Facility Contact</label>
                    <textarea class="form-control" id="parking_facility_contact" name="parking_facility_contact" rows="5"><?= $product['parking_facility_contact']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="what_to_do_when_you_arrive">What to do When You Arrive</label>
                    <textarea class="form-control" id="what_to_do_when_you_arrive" name="what_to_do_when_you_arrive" rows="3"><?= $product['what_to_do_when_you_arrive']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="what_to_do_when_you_return">What to do When You Return</label>
                    <textarea class="form-control" id="what_to_do_when_you_return" name="what_to_do_when_you_return" rows="3"><?= $product['what_to_do_when_you_return']; ?></textarea>
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="security_information">Security Information</label>
                    <textarea class="form-control" id="security_information" name="security_information" rows="3"><?= $product['security_information']; ?></textarea>
                  </div>
                </div>
                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                </div>
              </div>

              <div id="capacity-information-vertical" class="content" role="tabpanel" aria-labelledby="capacity-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Capacity Information</h5>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity">Product Capacity</label>
                    <input type="text" id="capacity" name="capacity" class="form-control number-input" placeholder="Product Capacity" value="<?= $product['capacity']; ?>" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="adjust_prices_by_capacity">Adjust Prices By Capacity</label>
                    <select class="select2" id="adjust_prices_by_capacity" name="adjust_prices_by_capacity">
                      <?php
                      $capacityby = get_capacity_by();
                      foreach ($capacityby as &$option) {
                        if ($product['adjust_prices_by_capacity'] == $option['value']) {
                          echo "<option selected value='" . $option['value'] . "'>" . $option['label'] . "</option>";
                        } else {
                          echo "<option value='" . $option['value'] . "'>" . $option['label'] . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_one">Capacity Threshold One (when capacity reaches in %)</label>
                    <input type="text" id="capacity_threshold_one" name="capacity_threshold_one" class="form-control number-input" value="<?= $product['capacity_threshold_one']; ?>" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_one_increase">Increase prices by(%)</label>
                    <input type="text" id="capacity_threshold_one_increase" name="capacity_threshold_one_increase" class="form-control  number-input" value="<?= $product['capacity_threshold_one_increase']; ?>" />
                  </div>
                </div>


                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_two">Capacity Threshold Two (When capacity reaches in %)</label>
                    <input type="text" id="capacity_threshold_two" name="capacity_threshold_two" class="form-control number-input" value="<?= $product['capacity_threshold_two']; ?>" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_two_increase">Increase prices by (%)</label>
                    <input type="text" id="capacity_threshold_two_increase" name="capacity_threshold_two_increase" class="form-control number-input" value="<?= $product['capacity_threshold_two_increase']; ?>" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-6">
                      <label class="form-label" for="capacity_threshold_two_increase">Time</label>
                      <select class="select2" id="get_limiter_time" name="get_limiter_time">
                      <?php
                      $get_limiter_time = get_limiter_time();
                      foreach ($get_limiter_time as $code => $name) {
                        if ($product['get_limiter_time'] == $code) {
                          echo "<option selected value='$code'>$name</option>";
                        } else {
                          echo "<option value='$code'>$name</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-12">
                    <h5 class="mb-0">Per Day Capacity Information</h5>
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Capacity Threshold Per Day</label>
                    <input type="text" id="capacity_threshold_day" name="capacity_threshold_day" class="form-control number-input" value="<?= ($product['capacity_threshold_day'])? $product['capacity_threshold_day']: 0; ?>"/>
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Replace To Product Code</label>
                    <input type="text" id="replace_product_code" name="replace_product_code" class="form-control" placeholder="Replace To Product Code" value="<?= $product['replace_product_code']; ?>">
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Minimum Days</label>
                    <input type="text" id="min_days" name="min_days" class="form-control number-input" placeholder="Minimum No. of Days" value="<?= $product['min_days']; ?>">
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Maximum Days</label>
                    <input type="text" id="max_days" name="max_days" class="form-control number-input" placeholder="Maximum No. of Days" value="<?= $product['max_days']; ?>">
                  </div>

                </div>


                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <a href="#" class="btn btn-primary btn-next">
                    <span class="align-middle d-sm-inline-block d-none">Next</span>
                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                  </a>
                  <!-- <button class="btn btn-success" type="submit" id="btnsubmit">Submit</button> -->
                </div>
              </div>

              <div id="map-information-vertical" class="content" role="tabpanel" aria-labelledby="map-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Google Map & Photo's</h5>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="map_link">Google Maps Embed Link</label>
                    <textarea class="form-control" id="map_link" name="map_link" rows="3"><?= $product['map_link']; ?></textarea>
                  </div>
                </div>
              </div>

              <div id="addons-information-vertical" class="content" role="tabpanel" aria-labelledby="addons-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Addons Information</h5>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <table class="datatables-ajax table table-responsive">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>                                
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                          <?php if ($addons):
                            foreach ($addons as $key => $ad):?>
                          <tr>
                            <td><?= $ad->addon_name?></td>
                            <td><?= $ad->addon_price?></td>
                            <td><?= $ad->addon_desc?></td>
                            <td>
                              <button type="button" class="btn btn-<?=($ad->addon_status == 1)? 'danger': 'success';?> btn-sm statusBtn" 
                                data-id="<?= $ad->id?>"
                                data-status="<?= ($ad->addon_status == 0)? 1: 0;?>"
                                ><?= ($ad->addon_status == 0)? 'Active': 'In-Active'?></button>

                              <button type="button" class="btn btn-primary btn-sm editBtn" 
                                data-id="<?= $ad->id?>"
                                data-name="<?= $ad->addon_name?>"
                                data-price="<?= $ad->addon_price?>"
                                data-desc="<?= $ad->addon_desc?>"
                                data-bname="Save"
                                >Duplicate</button>
                                <button type="button" class="btn btn-info btn-sm editBtn" 
                                data-id="<?= $ad->id?>"
                                data-name="<?= $ad->addon_name?>"
                                data-price="<?= $ad->addon_price?>"
                                data-desc="<?= $ad->addon_desc?>"
                                data-bname="Update"
                                >Edit</button>


                              <button type="button" class="btn btn-danger btn-sm deleteBtn" data-id="<?= $ad->id?>">Delete</button>
                            </td>
                          </tr>
                          <?php endforeach; endif;?>
                        </tbody>
                        <tbody>
                        </tbody>   
                    </table>
                  </div>
                </div>
                <div class="row" id="moreAddon">
                  <!-- <?php if ($addons):
                    foreach ($addons as $key => $ad):?>
                  
                  <div class="mb-1 col-md-6 addon<?= $key?>">
                    <label class="form-label">Addon Name</label>
                    <input type="text" name="addon_name[]" class="form-control" placeholder="Addon Name" value="<?= $ad->addon_name?>" />
                  </div>

                  <div class="mb-1 col-md-6 addon<?= $key?>">
                    <label class="form-label">Addon Price</label>
                    <input type="text" name="addon_price[]" class="form-control number-input" placeholder="Addon Price" value="<?= $ad->addon_price?>" />
                  </div>

                  <div class="mb-1 col-md-10 addon<?= $key?>">
                    <label class="form-label">Addon Description</label>
                    <textarea name="addon_desc[]" rows="3" class="form-control" placeholder="Addon Description..."><?= $ad->addon_desc?></textarea>
                  </div>
                  <div class="mt-3 col-md-2 addon<?= $key?>">
                    <button type="button" class="btn btn-danger waves-effect waves-float waves-light removeAddon" data-id="<?= $key?>">Remove</button>
                  </div>
                <?php endforeach; endif;?> -->
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4 mt-2"></div>
                  <div class="mb-1 col-md-3 mt-2 text-right">
                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light moreAddonBtn"><i data-feather="plus"></i> More Addons</button>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <a href="#" class="btn btn-primary btn-prev">
                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                  </a>
                  <button class="btn btn-success" type="submit" id="btnsubmit">Submit</button>
                </div>
              </div>


            </div>
          </div>
        </section>
      </form>
      <!-- /Vertical Wizard -->

    </div>
  </div>
</div>
<!-- END: Content-->

<?= $this->endSection(); ?>
<?= $this->section("javascript"); ?>
<script type="text/javascript">
  $("#frmsubmit").submit(function(event) {
    event.preventDefault();
    // var formData = $(this).serialize();
    var formData = new FormData(this);
    var logoFile = $("#logo")[0].files[0];
    formData.append('logo', logoFile);
    $.ajax({
      url: $(this).attr("action"),
      type: 'POST',
      dataType: 'json',
      data: formData,
      contentType: false,
      processData: false,
      async: true,
      cache: false,
      beforeSend: function() {
        $("#btnsubmit").attr("disabled", true);
      },
      success: function(data) {
        if (data.status) {
          toastr['success'](data.message, 'Success!', {
            closeButton: true,
            tapToDismiss: true,
            progressBar: true
          });
        } else {
          if (data.errors) {
            $.each(data.errors, function(key, value) {
              toastr['error'](value, 'Error!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true,
              });
            });
          } else {
            toastr['error'](data.message, 'Error!', {
              closeButton: true,
              tapToDismiss: true,
              progressBar: true,
            });
          }
        }
      },
      error: function(xhr) {
        $("#btnsubmit").attr("disabled", false);
      },
      complete: function() {
        $("#btnsubmit").attr("disabled", false);
      }
    });
  });

  // $('.price-input').on('input', function() {
  //     // Get the current value of the input
  //     var inputValue = $(this).val();
      
  //     // Remove any character that is not a digit or decimal point
  //     inputValue = inputValue.replace(/[^0-9.]/g, '');
      
  //     // Allow only one decimal point
  //     if ((inputValue.match(/\./g) || []).length > 1) {
  //         inputValue = inputValue.replace(/\.+$/, '');
  //     }
      
  //     // Prevent leading zero before a decimal point (e.g., '00.5' -> '0.5')
  //     inputValue = inputValue.replace(/^0+(\d)/, '$1');
      
  //     $(this).val(inputValue);
  // });
  $('.price-input').on('input', function () {
      let v = $(this).val();

      // Keep only digits, dot and minus
      v = v.replace(/[^0-9.\-]/g, '');

      // Allow only ONE leading minus
      v = v
        .replace(/(?!^)-/g, '')   // drop any '-' that isn't the first char
        .replace(/^-{2,}/, '-');  // collapse multiple leading '-'

      // Allow only ONE decimal point
      const firstDot = v.indexOf('.');
      if (firstDot !== -1) {
        v = v.slice(0, firstDot + 1) + v.slice(firstDot + 1).replace(/\./g, '');
      }

      // Normalize edge cases
      v = v.replace(/^-\./, '-0.'); // "-."  -> "-0."
      v = v.replace(/^\./, '0.');   // "."   -> "0."
      v = v.replace(/^(-?)0+(\d)/, '$1$2'); // "00.5" -> "0.5", "-00.5" -> "-0.5"

      $(this).val(v);
  });

  const numberInputs = document.querySelectorAll(".number-input");
  numberInputs.forEach(function(input) {
    input.addEventListener("input", function() {
      const value = input.value;
      input.value = value.replace(/[^0-9.]/g, "");
    });
  });

  const alphanumericInputs = document.querySelectorAll(".alphanumeric-input");
  alphanumericInputs.forEach(function(input) {
    input.addEventListener("input", function() {
      const value = input.value;
      input.value = value.replace(/[^a-zA-Z0-9]/g, ""); // Allowing only alphanumeric characters
    });
  });

  function submitForm() {
      // Get values by ID
      var id = $('#change').val();

      // Create an object with the data
      var data = {
          id: id

      };

      // Send an AJAX request
      $.ajax({
          type: "GET",
          url: "<?= url_to('products/get_assign_value'); ?>", // Replace with the actual URL to handle the request
          data: data,
          success: function(response) {
              console.log(response);
              $("#trunks").html(response);

              if (data.msg) {


              } else {



              }
              // Handle the AJAX response here




          },
          error: function(error) {
              // Handle errors here
              console.error(error);
          }
      });
  }
  $(document).ready(function () {
      if (localStorage.getItem('ajaxReload') === 'true') {
        // Use a timeout to ensure dynamic content is loaded
        setTimeout(() => {
          $('.step').removeClass('active').addClass('crossed');
          $('.content').removeClass('active');
          $('#addons-information-vertical').addClass('active dstepper-block');
          $('#addons-information-vertical-trigger').removeClass('crossed').addClass('active');
        }, 500); // Adjust delay if needed
        localStorage.removeItem('ajaxReload'); // Clean up
      }
  });
  // Add more Addons
  let count=<?= ($addons)? count($addons):1;?>;
  $('.moreAddonBtn').on('click', function()
    {
        count++;
        let html ='<div class="mb-1 col-md-6 addon'+count+'">'+
                      '<label class="form-label">Addon'+count+' Name</label>'+
                      '<input type="text" id="addon_name'+count+'" class="form-control" placeholder="Addon'+count+' Name" />'+
                    '</div><div class="mb-1 col-md-6 addon'+count+'">'+
                      '<label class="form-label">Addon'+count+' Price</label>'+
                      '<input type="text" id="addon_price'+count+'" class="form-control" placeholder="Addon'+count+' Price" />'+
                    '</div>'+
                    '</div><div class="mb-1 col-md-9 addon'+count+'">'+
                      '<label class="form-label">Addon'+count+' Description</label>'+
                      '<textarea id="addon_desc'+count+'" rows="3" class="form-control" placeholder="Addon Description..."></textarea>'+
                    '</div><div class="mt-3 col-md-3 addon'+count+'">'+
                      '<button type="button" class="btn btn-success btn-sm addAddon" data-id="'+count+'">Save</button> '+
                      '<button type="button" class="btn btn-danger btn-sm removeAddon" data-id="'+count+'">Remove</button>'+
                    '</div>';
        $('#moreAddon').append(html);
    });
  // Remove Append Addon
  $('#moreAddon').on('click', '.removeAddon', function () {
    let id = $(this).data('id');
    $('.addon'+id).remove();
  });
  // New Addon add
  $('#moreAddon').on('click', '.addAddon', function () {
    let id = $(this).data('id');
    let product_id= <?= $product['id']?>;
    let name = $('#addon_name'+id).val();
    let price = $('#addon_price'+id).val();
    let desc = $('#addon_desc'+id).val();

    if (product_id && name && price) {
        $.ajax({
            url: "<?= url_to('products/addons/add'); ?> ",
            type: 'GET',
            data: {
                product_id : product_id,
                name: name,
                price: price,
                desc: desc,
            },
            complete: function (data) {
              console.log('response',data.responseJSON);
              toastr['success'](data.responseJSON.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              localStorage.setItem('ajaxReload', 'true');
              location.reload();
            }
        });
    }
  });
  // Addon Duplicate
  $('#moreAddon').on('click', '.BtnSave', function () {
    let id = $(this).data('id');
    let product_id= <?= $product['id']?>;
    let name = $('#addon_name'+id).val();
    let price = $('#addon_price'+id).val();
    let desc = $('#addon_desc'+id).val();

    if (product_id && name && price) {
        $.ajax({
            url: "<?= url_to('products/addons/add'); ?> ",
            type: 'GET',
            data: {
                product_id : product_id,
                name: name,
                price: price,
                desc: desc,
            },
            complete: function (data) {
              console.log('response',data.responseJSON);
              toastr['success'](data.responseJSON.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              localStorage.setItem('ajaxReload', 'true');
              location.reload();
            }
        });
    }
  });
  // Addon Edit
  $('.editBtn').on('click', function() {
    let id = $(this).data('id');
    let addon_name = $(this).data('name');
    let addon_price = $(this).data('price');
    let addon_desc = $(this).data('desc');

    let btnName = $(this).data('bname');
    if ($('.addon'+id).length == 0) {
      if (id) {
          let html ='<div class="mb-1 col-md-6 addon'+id+'">'+
                      '<label class="form-label">Addon Name</label>'+
                      '<input type="text" id="addon_name'+id+'" class="form-control" placeholder="Addon Name" value="'+ addon_name+'"/>'+
                    '</div><div class="mb-1 col-md-6 addon'+id+'">'+
                      '<label class="form-label">AddonPrice</label>'+
                      '<input type="text" id="addon_price'+id+'" class="form-control" placeholder="Addon Price" value="'+ addon_price+'"/>'+
                    '</div>'+
                    '</div><div class="mb-1 col-md-9 addon'+id+'">'+
                      '<label class="form-label">Addon Description</label>'+
                      '<textarea id="addon_desc'+id+'" rows="3" class="form-control" placeholder="Addon Description...">'+addon_desc+'</textarea>'+
                    '</div><div class="mt-3 col-md-3 addon'+id+'">'+
                      '<button type="button" class="btn btn-info btn-sm Btn'+btnName+'" data-id="'+id+'">'+btnName+'</button> '+
                      '<button type="button" class="btn btn-danger btn-sm removeAddon" data-id="'+id+'">Remove</button>'+
                    '</div>';
          $('#moreAddon').append(html);
      }
    }
  });
  // Addon Update
  $('#moreAddon').on('click', '.BtnUpdate', function () {
    let id = $(this).data('id');
    let name = $('#addon_name'+id).val();
    let price = $('#addon_price'+id).val();
    let desc = $('#addon_desc'+id).val();

    if (id) {
        $.ajax({
            url: "<?= url_to('products/addons/update'); ?> ",
            type: 'GET',
            data: {
                id : id,
                name: name,
                price: price,
                desc: desc,
            },
            complete: function (data) {
              console.log('response',data.responseJSON);
              toastr['success'](data.responseJSON.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              localStorage.setItem('ajaxReload', 'true');
              location.reload();
            }
        });
    }
  });
  // Addon Update Status
  $('.statusBtn').on('click', function() {
    let id = $(this).data('id');
    let status = $(this).data('status');
    if (id) {
        $.ajax({
            url: "<?= url_to('products/addons/status'); ?> ",
            type: 'GET',
            data: {
                id : id,
                status: status
            },
            complete: function (data) {
              console.log('response',data.responseJSON);
              toastr['success'](data.responseJSON.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              localStorage.setItem('ajaxReload', 'true');
              location.reload();
            }
        });
    }
  });
  // Addon Delete
  $('.deleteBtn').on('click', function() {
    let id = $(this).data('id');
    if (id) {
        $.ajax({
            url: "<?= url_to('products/addons/delete'); ?> ",
            type: 'GET',
            data: {
                id : id,
            },
            complete: function (data) {
              console.log('response',data.responseJSON);
              toastr['success'](data.responseJSON.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              localStorage.setItem('ajaxReload', 'true');
              location.reload();
            }
        });
    }
  });

</script>
<?= $this->endSection(); ?>