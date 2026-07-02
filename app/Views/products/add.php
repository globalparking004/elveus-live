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
      <form action="<?= base_url('products/save'); ?>" method="POST" id="frmsubmit">
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
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="product_code">Product Code</label>
                    <input type="text" id="product_code" name="product_code" class="form-control alphanumeric-input" placeholder="Product Code" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Name" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="name_ar">Arabic Name</label>
                    <input type="text" id="name_ar" name="name_ar" class="form-control" placeholder="Name" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="name">Telephone</label>
                    <input type="text" id="telephone" name="telephone" class="form-control number-input" placeholder="Telephone" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-8">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Address" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="postcode">Post Code</label>
                    <input type="text" id="postcode" name="postcode" class="form-control" placeholder="Post Code" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="parent">Airport/Supplier</label>
                    <?php $airports = get_airports(); ?>
                    <select class="select2" id="parent" name="parent">
                      <?php
                      echo "<option value='*'>NONE</option>";
                      foreach ($airports as $code => $name) {
                        echo "<option value='$code'>$name</option>";
                      }
                      echo "<option value='*'>----- select Supplier -----</option>";
                      foreach ($suppliers as $supplier) {

                        $s_code=$supplier['code'];
                        $s_name=$supplier['name'];
                        echo "<option value='$s_code'>Supplier $s_name</option>";
                        
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="airport">API-Airport</label>
                    <?php $airports = get_airports(); ?>
                    <select class="select2" id="airport" name="airport">
                      <?php
                      echo "<option value='*'>NONE</option>";
                      foreach ($airports as $code => $name) {
                        echo "<option value='$code'>$name</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 form-password-toggle col-md-2">
                    <label class="form-label" for="latitude">Latitude</label>
                    <input type="text" id="latitude" name="latitude" class="form-control number-input" placeholder="Latitude" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-2">
                    <label class="form-label" for="longitude">Longitude</label>
                    <input type="text" id="longitude" name="longitude" class="form-control number-input" placeholder="Longitude" />
                  </div>
                  <div class="mb-1 form-password-toggle col-md-4">
                    <label class="form-label" for="operator_id">Operator</label>
                    <select class="select2" id="operator_id" name="operator_id">
                      <?php
                      echo "<option value='*'>NONE</option>";
                      for ($i = 0; $i < sizeof($operators); $i++) {
                        echo "<option value='" . $operators[$i]['id'] . "'>" . $operators[$i]['description'] . "</option>";
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
                      <input class="form-control" type="file" name="logo" id="logo">
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="logo1">New Website Logo</label>
                      <input class="form-control" type="file" name="logo1" id="logo1">
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="distance_miles">Distance to Airport (miles)</label>
                    <input type="text" id="distance_miles" name="distance_miles" class="form-control number-input" placeholder="Distance to Airport (miles)" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="transfer_time">Transfer Time</label>
                    <input type="text" id="transfer_time" name="transfer_time" class="form-control number-input" placeholder="Transfer Time" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="customize1">Customize 1</label>
                    <input type="text" id="customize1" name="customize1" class="form-control" placeholder="Customize1" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="customize1">Customize 2</label>
                    <input type="text" id="customize2" name="customize2" class="form-control" placeholder="Customize2" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4" style="display:none;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="is_none_amendable" name="is_none_amendable" value="checked" />
                      <label class="form-check-label" for="is_none_amendable">Is None Amendable</label>
                    </div>
                  </div>
                  <div class="mb-1 col-md-4">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input product_type" type="radio" id="meet_and_greet" name="product_type" value="Meet & Greet" />
                      <label class="form-check-label" for="meet_and_greet">Meet and Greet</label>
                    </div>
                  </div>

                  <!-- <div class="mb-1 col-md-2" style="display: none;">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="on_airport" name="on_airport" value="On Airport" />
                      <label class="form-check-label" for="on_airport">On Airport</label>
                    </div>
                  </div> -->

                  <div class="mb-1 col-md-3">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input product_type" type="radio" id="park_mark" name="product_type" value="Park & Ride" />
                      <label class="form-check-label" for="park_mark">Park and Ride</label>
                    </div>
                  </div>

                  <div class="mb-1 col-md-2">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input product_type" type="radio" name="product_type" value="Station" />
                      <label class="form-check-label" for="station">Station</label>
                    </div>
                  </div>

                  <div class="mb-1 col-md-3">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input product_type" type="radio" name="product_type" value="Park & Stroll" />
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
                    <input type="text" id="notice_period" name="notice_period" class="form-control number-input" placeholder="Notice Period (Hours)" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="opening_time">Opening Time</label>
                    <select class="select2" id="opening_time" name="opening_time">

                      <?php
                      foreach ($shifts as $code => $name) {
                        echo "<option value='$code'>$name</option>";
                      }
                      ?>

                    </select>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="closing_time">Closing Time</label>
                    <select class="select2" id="closing_time" name="closing_time">
                      <?php
                      foreach ($shifts as $code => $name) {
                        echo "<option value='$code'>$name</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="commission">Commission</label>
                    <input type="text" id="commission" name="commission" class="form-control number-input" placeholder="Commission" />
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="exclusive_to_website_id">Exclusively For Website</label>
                    <select class="select2" id="exclusive_to_website_id" name="exclusive_to_website_id">
                      <option value="*">ALL</option>

                      <?php foreach ($websites as $website) {
                        echo "<option value='$website->id'>$website->web_name</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="linked_product_code">Link Pricing To Product Code</label>
                    <input type="text" id="linked_product_code" name="linked_product_code" class="form-control" placeholder="Link Pricing To Product Code" />
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="linked_price">Adjust Linked Prices By +/-</label>
                    <input type="text" id="linked_price" name="linked_price" class="form-control price-input" placeholder="Adjust Linked Prices" value="0.00" />
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
                    <textarea class="form-control" id="directions" name="directions" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="directions_ar">Directions Arabic</label>
                    <textarea class="form-control" id="directions_ar" name="directions_ar" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="introduction">Introduction</label>
                    <textarea class="form-control" id="introduction" name="introduction" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="introduction_ar">Introduction Arabic</label>
                    <textarea class="form-control" id="introduction_ar" name="introduction_ar" rows="3"></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="information">Information</label>
                    <textarea class="form-control" id="information" name="information" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="information_ar">Information Arabic</label>
                    <textarea class="form-control" id="information_ar" name="information_ar" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="security_measures">Security Measures</label>
                    <textarea class="form-control" id="security_measures" name="security_measures" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="security_measures_ar">Security Measures Arabic</label>
                    <textarea class="form-control" id="security_measures_ar" name="security_measures_ar" rows="3"></textarea>
                  </div>
                </div>


                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="arrival_procedures">Arrival Procedures</label>
                    <textarea class="form-control" id="arrival_procedures" name="arrival_procedures" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="arrival_procedures_ar">Arrival Procedures Arabic</label>
                    <textarea class="form-control" id="arrival_procedures_ar" name="arrival_procedures_ar" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="departure_procedures">Departure Procedures</label>
                    <textarea class="form-control" id="departure_procedures" name="departure_procedures" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="departure_procedures_ar">Departure Procedures Arabic</label>
                    <textarea class="form-control" id="departure_procedures_ar" name="departure_procedures_ar" rows="3"></textarea>
                  </div>
                </div>


                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="disabled_facilities">Disabled Facilities</label>
                    <textarea class="form-control" id="disabled_facilities" name="disabled_facilities" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="disabled_facilities_ar">Disabled Facilities Arabic</label>
                    <textarea class="form-control" id="disabled_facilities_ar" name="disabled_facilities_ar" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="transfers">Transfers</label>
                    <textarea class="form-control" id="transfers" name="transfers" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="transfers_ar">Transfers Arabic</label>
                    <textarea class="form-control" id="transfers_ar" name="transfers_ar" rows="3"></textarea>
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
                    <input type="text" id="score_price" name="score_price" class="form-control number-input" placeholder="Price Score" value="0" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_accessibility">Accessibility Score</label>
                    <input type="text" id="score_accessibility" name="score_accessibility" class="form-control number-input" placeholder="Accessibility Score" value="0" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_efficiency">Efficiency Score</label>
                    <input type="text" id="score_efficiency" name="score_efficiency" class="form-control number-input" placeholder="Efficiency Score" value="0" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="score_security">Security Score</label>
                    <input type="text" id="score_security" name="score_security" class="form-control number-input" placeholder="Accessibility Score" value="0" />
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
                <div class="content-header d-flex justify-content-between">
                  <h5 class="mb-0">Email Confirmation</h5>
                  <button type="button" class="btn btn-primary btn-emailConfig">
                    <i data-feather="plus" class="align-middle me-sm-25 me-0"></i> 
                    <span class="align-middle d-sm-inline-block d-none">Add More</span>
                  </button>
                </div>
                <div class="row">
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="useful_information">Useful Information</label>
                    <textarea class="form-control" id="useful_information" name="useful_information" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-4">
                    <label class="form-label" for="driver_contact">Driver Contact Number</label>
                    <input type="text" name="driver_contact" id="driver_contact" class="form-control">
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="parking_facility_contact">Parking Facility Contact</label>
                    <textarea class="form-control" id="parking_facility_contact" name="parking_facility_contact" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="what_to_do_when_you_arrive">What to do When You Arrive</label>
                    <textarea class="form-control" id="what_to_do_when_you_arrive" name="what_to_do_when_you_arrive" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="what_to_do_when_you_return">What to do When You Return</label>
                    <textarea class="form-control" id="what_to_do_when_you_return" name="what_to_do_when_you_return" rows="3"></textarea>
                  </div>
                  <div class="mb-1 col-md-12">
                    <label class="form-label" for="security_information">Security Information</label>
                    <textarea class="form-control" id="security_information" name="security_information" rows="3"></textarea>
                  </div>
                </div>
                <div class="row" id="moreEmailConfig"></div>
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
                    <input type="text" id="capacity" name="capacity" class="form-control number-input" placeholder="Product Capacity" value="0" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="adjust_prices_by_capacity">Adjust Prices By Capacity</label>
                    <select class="select2" id="adjust_prices_by_capacity" name="adjust_prices_by_capacity">
                      <?php
                      $capacityby = get_capacity_by();
                      foreach ($capacityby as &$option) {
                        echo "<option value='" . $option['value'] . "'>" . $option['label'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_one">Capacity Threshold One (when capacity reaches in %)</label>
                    <input type="text" id="capacity_threshold_one" name="capacity_threshold_one" class="form-control number-input" value="0" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_one_increase">Increase prices by(%)</label>
                    <input type="text" id="capacity_threshold_one_increase" name="capacity_threshold_one_increase" class="form-control number-input" value="0" />
                  </div>
                </div>


                <div class="row">
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_two">Capacity Threshold Two (When capacity reaches in %)</label>
                    <input type="text" id="capacity_threshold_two" name="capacity_threshold_two" class="form-control number-input" value="0" />
                  </div>
                  <div class="mb-1 col-md-6">
                    <label class="form-label" for="capacity_threshold_two_increase">Increase prices by (%)</label>
                    <input type="text" id="capacity_threshold_two_increase" name="capacity_threshold_two_increase" class="form-control number-input" value="0" />
                  </div>
                </div>

                <div class="row">
                  <div class="mb-1 col-md-12">
                    <h5 class="mb-0">Per Day Capacity Information</h5>
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Capacity Threshold Per Day</label>
                    <input type="text" id="capacity_threshold_day" name="capacity_threshold_day" class="form-control number-input" value="0" />
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Replace To Product Code</label>
                    <input type="text" id="replace_product_code" name="replace_product_code" class="form-control" placeholder="Replace To Product Code">
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Minimum Days</label>
                    <input type="text" id="min_days" name="min_days" class="form-control number-input" placeholder="Minimum No. of Days">
                  </div>
                  <div class="mb-1 col-md-3">
                    <label class="form-label">Maximum Days</label>
                    <input type="text" id="max_days" name="max_days" class="form-control number-input" placeholder="Maximum No. of Days">
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
                    <textarea class="form-control" id="map_link" name="map_link" rows="3"></textarea>
                  </div>
                </div>
              </div>

              <div id="addons-information-vertical" class="content" role="tabpanel" aria-labelledby="addons-information-vertical-trigger">
                <div class="content-header">
                  <h5 class="mb-0">Addons Information</h5>
                </div>
                <div class="row" id="moreAddon"></div>
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
                  <button class="btn btn-success" type="submit" id="btnsubmit"><i data-feather="check"></i> Submit</button>
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
          $("#frmsubmit")[0].reset();
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


  var validator = $(".form-crud").validate({
    rules: {
      'description': {
        required: vdstatus,
        minlength: 2
      },
      'capacity': {
        required: vdstatus,
        minlength: 1
      }
    },
    messages: {
      "description": {
        required: "Please enter your description",
        minlength: "description must be 2 char long"
      },
      "capacity": {
        required: "Please enter your capacity",
        minlength: "capacity must be 1 char long"
      }
    },
    submitHandler: function(form) {
      var formData = $(form).serialize();
      $.ajax({
        url: $(form).attr("action"),
        type: 'POST',
        dataType: 'json',
        data: formData,
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
            table.draw();
            hideModal("add-xlarge");
            $(form)[0].reset();
          } else {
            if (data.errors) {
              validator.showErrors(data.errors);
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
      return false;
    }
  });

  function edit_data(id) {
    $.ajax({
      url: "<?= base_url('operators/get_record'); ?>",
      type: 'GET',
      dataType: 'json',
      data: "id=" + encodeURIComponent(id),
      beforeSend: function() {
        $("#form-crud-title").html("Modify Server");
        $(".clpassword").hide();
      },
      success: function(res) {
        if (res.status) {
          $('#status').val('');
          $("#id").val(id);
          $("#description").val(res.data.description);
          $("#capacity").val(res.data.capacity);
          $('#status').val(res.data.status);
          $('#status').trigger('change');
          $("#form-crud").attr("action", "<?= base_url('operators/update'); ?>");
          showModal("add-xlarge");
        }
      },
      error: function(xhr) {

      },
      complete: function() {

      }
    });

  }

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
    }).then(function(result) {
      if (result.value) {
        $.ajax({
          url: "<?= base_url('operators/delete_record'); ?>",
          type: 'GET',
          dataType: 'json',
          data: "id=" + encodeURIComponent(id),
          beforeSend: function() {

          },
          success: function(data) {
            if (data.status) {
              toastr['success'](data.message, 'Success!', {
                closeButton: true,
                tapToDismiss: true,
                progressBar: true
              });
              table.draw();
            } else {
              if (data.errors) {
                validator.showErrors(data.errors);
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

          },
          complete: function() {

          }
        });
      }
    });
  }

  $("#add-xlarge").on("hidden.bs.modal", function() {
    $("#form-crud")[0].reset();
    $("#id").val('');
    $("#form-crud-title").html("Add Operators");
    $("#form-crud").attr("action", "<?= base_url('operators/save'); ?>");
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
      
  //     // Update the input value
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
  // Add more Email confirmation
  let count=1;
  $('.btn-emailConfig').on('click', function()
  {
      if (!$('#moreEmailConfig').html()) 
      {
        count++;
        let html ='<div class="mb-1 col-md-12">'+
                      '<label class="form-label">Useful Information'+count+'</label>'+
                      '<textarea class="form-control" id="useful_information" name="useful_information" rows="3"></textarea>'+
                      '</div><div class="mb-1 col-md-12">'+
                      '<label class="form-label">Parking Facility Contact'+count+'</label>'+
                      '<textarea class="form-control" id="parking_facility_contact" name="parking_facility_contact" rows="3"></textarea>'+
                      '</div><div class="mb-1 col-md-12">'+
                      '<label class="form-label">What to do When You Arrive'+count+'</label>'+
                      '<textarea class="form-control" id="what_to_do_when_you_arrive"   name="what_to_do_when_you_arrive" rows="3"></textarea>'+
                      '</div><div class="mb-1 col-md-12">'+
                      '<label class="form-label">What to do When You Return'+count+'</label>'+
                      '<textarea class="form-control" id="what_to_do_when_you_return" name="what_to_do_when_you_return" rows="3"></textarea>'+
                      '</div><div class="mb-1 col-md-10">'+
                      '<label class="form-label">Security Information'+count+'</label>'+
                      '<textarea class="form-control" id="security_information" name="security_information" rows="3"></textarea>'+
                      '</div><div class="mt-3 col-md-2">'+
                      '<button type="button" class="btn btn-danger waves-effect waves-float waves-light removeEmailConfig" data-id="'+count+'">Remove</button>'+
                    '</div>';
        $('#moreEmailConfig').append(html);
      }
      count=1;
  });
  $('#moreEmailConfig').on('click', '.removeEmailConfig', function () {
    $('#moreEmailConfig').html('');
  });
  // Add more Addons
  let count1=0;
  $('.moreAddonBtn').on('click', function()
    {
        count1++;
        let html ='<div class="mb-1 col-md-6  addon'+count1+'">'+
                      '<label class="form-label">Addon'+count1+' Name</label>'+
                      '<input type="text" name="addon_name[]" class="form-control" placeholder="Addon'+count1+' Name" />'+
                    '</div><div class="mb-1 col-md-6  addon'+count1+'">'+
                      '<label class="form-label">Addon'+count1+' Price</label>'+
                      '<input type="text" name="addon_price[]" class="form-control" placeholder="Addon'+count1+' Price" />'+
                    '</div>'+
                    '</div><div class="mb-1 col-md-10  addon'+count1+'">'+
                      '<label class="form-label">Addon'+count1+' Description</label>'+
                      '<textarea name="addon_desc[]" rows="3" class="form-control" placeholder="Addon Description..."></textarea>'+
                      '</div><div class="mt-3 col-md-2 addon'+count1+'">'+
                      '<button type="button" class="btn btn-danger waves-effect waves-float waves-light removeAddon" data-id="'+count1+'">Remove</button>'+
                    '</div>';
        $('#moreAddon').append(html);
  });
  $('#moreAddon').on('click', '.removeAddon', function () {
    let id = $(this).data('id');
    console.log('id: ',id);
    $('.addon'+id).remove();
  });

</script>
<?= $this->endSection(); ?>