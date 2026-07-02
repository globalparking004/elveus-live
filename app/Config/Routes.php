<?php
namespace Config;
// Create a new instance of our RouteCollection class.
$routes = Services::routes();
/*
 * --------------------------------------------------------------------
 * Router Setup 
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */


 $routes->get('/server_insta_giveaway/public/password_recovery/get_values_from_email/79c89819528d4414f84e65004bc9d2c8/a9772108@gmail.com', 'Users::get_values_from_email');


// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('',['filter'=>'UnAuthFilter'],function($routes){
    $routes->get('/', 'Login::index');
    $routes->get('/login', 'Login::index');
    $routes->post('/login/auth','Login::auth');
    $routes->post('/login/auth2','Login::auth2');
    $routes->post('/login/verifyOtp','Login::verifyOtp');
    $routes->get('/login/defaultUser', 'Login::defaultUser');
    $routes->get('/set_password', 'Login::set_password');
    $routes->post('/password/update', 'Login::update_password');
});

$routes->get('test_limit_date', 'ApiController::test_limit_date');
$routes->get('review/get', 'ApiController::get_reviews');
$routes->get('review/add', 'ApiController::add_review');
$routes->get('passenger/update', 'ApiController::update_passenger');

////////////////////////////// supplier apis /////////////////////////
$routes->get('get_product_info', 'supplier_api_controller::show_product_supplier');
$routes->get('get_product_info1', 'supplier_api_controller::show_product_supplier1');

$routes->get('insert_product_Booking', 'supplier_api_controller::index');
$routes->get('update_product_Booking', 'supplier_api_controller::update_product_Booking');
/////////////////////////////////////////////////////////////////////

$routes->get('process_payment', 'stripe::index');
$routes->get('process_payment_test', 'stripe::test');

$routes->group('',['filter'=>'AuthFilter'],function($routes){
    $routes->get('/', 'Dashboard::index');
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/database_export', 'Dashboard::export_database');
    $routes->get('/booking_export', 'Dashboard::export_bookings'); 
    $routes->get('/settings', 'Dashboard::settings'); 
    $routes->post('/settings/save', 'Dashboard::settings_save');
    $routes->get('/gopakistan', 'Dashboard::gopakistan');
    $routes->get('/gopakistan/get', 'Dashboard::gopakistan_get');

    $routes->get('/account/website', 'Account::index');
    $routes->get('/account/get', 'Account::get_account');
    $routes->get('/account/get2', 'Account::get_account2');
    $routes->post('/account/add', 'Account::add_account');
    $routes->get('/account/delete', 'Account::delete_account');

    $routes->get('/account/supplier', 'Account::account_supplier');
    $routes->get('/account/supplier/get', 'Account::get_account_supplier');
    $routes->get('/account/supplier/get2', 'Account::get_account_supplier2');
    
    $routes->get('/account/download', 'Account::downlaod_account_bookings');
    $routes->get('screenshot/(:any)', 'Account::viewScreenshot/$1');

    $routes->get('/dashboard/get_stastics', 'Dashboard::get_stastics'); 
    $routes->get('/dashboard/get_stastics2', 'Dashboard::get_stastics2'); 
    $routes->get('/dashboard/get_go_stastics', 'Dashboard::get_go_stastics'); 
    $routes->get('/reviews', 'Dashboard::reviews'); 
    $routes->get('/reviews/get', 'Dashboard::reviews_get'); 
    $routes->get('/reviews/publish', 'Dashboard::reviews_publish'); 
    $routes->get('/reviews/delete', 'Dashboard::reviews_delete');
    
    $routes->get('/profile','Profile::index');
    $routes->post('/profile/save','Profile::save');
    $routes->post('/profile/change_password','Profile::change_password');

    $routes->get('/users', 'Users::index');
    $routes->get('/users/get', 'Users::get');    
    $routes->get('/users/get_roles', 'Users::get_roles');    
    $routes->get('/users/get_record', 'Users::get_record');  
    $routes->get('/users/delete_record', 'Users::delete_record');
    $routes->get('/users/logout', 'Users::logout');
    $routes->post('/users/save', 'Users::save');
    $routes->post('/users/update', 'Users::update');
    $routes->post('/users/reset_password', 'Users::reset_password');
    $routes->get('/users/logs', 'Users::user_logs');
    $routes->get('/users/devices', 'Users::get_devices');
    $routes->get('/users/devices/logout', 'Users::device_logout');

    

    $routes->get('/operators', 'Operators::index');
    $routes->get('/operators/get', 'Operators::get');
    $routes->get('/operators/get_record', 'Operators::get_record');
    $routes->get('/operators/delete_record', 'Operators::delete_record');
    $routes->post('/operators/save', 'Operators::save');
    $routes->post('/operators/update', 'Operators::update');

    ////////////////// suppleir //////////////////////////


    $routes->get('/supplier', 'supplier::index');
    $routes->post('/supplier/save', 'supplier::save');
    $routes->get('/supplier/get', 'supplier::get');
    $routes->get('/supplier/get_record', 'supplier::get_record');
    $routes->post('/supplier/update', 'supplier::update');
    $routes->get('/supplier/delete_record', 'supplier::delete_record');


    //////////////////////////////////////////////////////////////
    $routes->get('/products', 'Products::index');
    $routes->get('/products/get', 'Products::get');
    $routes->get('/products/add', 'Products::add');
    $routes->get('/products/edit', 'Products::edit');
    $routes->get('/products/duplicate', 'Products::duplicate');
    $routes->get('/products/delete_record', 'Products::delete_record');
    $routes->post('/products/save','Products::save');
    $routes->post('/products/update','Products::update');

    $routes->get('/products/range', 'Products::range');
    $routes->get('/products/get_rate_cards', 'Products::get_rate_cards');
    
    $routes->post('/products/add_band','Products::add_band');   
    $routes->post('/products/import_band','Products::import_band');   
    $routes->get('/products/get_band','Products::get_band');
    $routes->get('/products/download_band','Products::download_band');
    $routes->get('/products/delete_band','Products::delete_band');
    
    $routes->post('/products/add_ranges','Products::add_ranges');   
    $routes->get('/products/get_bands_ranges','Products::get_bands_ranges');
    $routes->get('/products/get_ranges','Products::get_ranges');    
    $routes->get('/products/edit_range','Products::edit_range'); 
    $routes->get('/products/delete_range','Products::delete_range');


    $routes->get('/products/get_close_outs','Products::get_close_outs');
    
    $routes->get('/products/get_assign_value','Products::get_assign_value');

    $routes->get('/products/addons/add','Products::addon_add');
    $routes->get('/products/addons/update','Products::addon_update');
    $routes->get('/products/addons/status','Products::addon_status');
    $routes->get('/products/addons/delete','Products::addon_delete');
    // Reports
    $routes->get('/reports', 'Reports::index');
    $routes->get('/reports/exports', 'Reports::exports');

    $routes->get('/bookings/capacity/download', 'Reports::capacity_download');

    $routes->get('/reports/all_bookings', 'Reports::all_booking');
    $routes->get('/reports/bookings/get', 'Reports::get_bookings');
    $routes->get('/reports/bookings/get_websites', 'Reports::get_websites');

    $routes->get('/reports/performance', 'Reports::performance');
    $routes->get('/reports/performance/get', 'Reports::get_performance');
    $routes->get('/reports/performance/get_performance_supplier', 'Reports::get_performance_supplier');
    $routes->get('/reports/performance/get_go_performance', 'Reports::get_go_performance');
    $routes->get('/reports/aff-performance', 'Reports::aff_performance');
    $routes->get('/reports/aff-performance/get', 'Reports::get_aff_performance');
    $routes->get('/reports/aff-performance/get-go', 'Reports::get_go_aff_performance');
    $routes->get('/reports/aff-performance/get-web', 'Reports::get_aff_webperformance');
    $routes->get('/reports/get_airport_by_supplier', 'Reports::get_airport_by_supplier');

    $routes->get('/reports/departure_return', 'Reports::departure_return');
    $routes->get('/reports/departure_return/get', 'Reports::get_departure_return');

    $routes->get('/reports/refunds', 'Reports::refunds');
    $routes->get('/reports/refunds/get', 'Reports::get_refunds');
    // Driver Dashboard
    $routes->get('/reports/passenger', 'Reports::passenger');
    $routes->get('/reports/passenger/get', 'Reports::get_passenger');

    $routes->get('/reports/bookings_count', 'Reports::bookings_count');
    $routes->get('/reports/bookings_count/get', 'Reports::get_bookings_count');

    $routes->get('/reports/driver/bookings', 'Reports::get_booking_by_time');

    $routes->post('/products/add_close_out','Products::add_close_out');
    $routes->get('/products/edit_close_out','Products::edit_close_out');
    $routes->get('/products/delete_close_out','Products::delete_close_out');

    $routes->get('/integration/clicksend', 'Integration::index');
    $routes->get('/clicksend/get', 'Integration::get');
    $routes->get('/clicksend/get_template', 'Integration::get_template');
    $routes->get('/clicksend/sms_sent', 'Integration::sent');
    $routes->post('/clicksend/sms_sent1', 'Integration::sent1');

    // invoices
    $routes->get('/invoices', 'Invoices::index');

    $routes->get('/invoices/admin', 'Invoices::admin_invoice');
    $routes->get('/invoices/admin/get', 'Invoices::get_admin_invoice');
    $routes->get('/invoices/admin/get-supplier-dinvoice', 'supplier::get_admin_invoice');
    $routes->get('/invoices/admin/get-supplier-airport-invoice', 'supplier::get_admin_airport_invoice');
    $routes->get('/invoices/admin/generate', 'Invoices::admin_generate_invoice');

    $routes->get('/invoices/operator', 'Invoices::operator_invoice');
    $routes->get('/invoices/operator/get', 'Invoices::get_operator_invoice');
    $routes->get('/invoices/operator/get-supplier-dinvoice', 'Operators::get_operator_invoice');
    $routes->get('/invoices/operator/generate', 'Invoices::operator_generate_invoice');

    
    $routes->get('/invoices/operator/get-invoice-data', 'Operators::get_invoice_data');

    $routes->get('/invoices/apply_gcost', 'Invoices::apply_gcost');
    $routes->get('/invoices/get_apply_gcost', 'Invoices::get_apply_gcost');

    $routes->get('/invoices/paid', 'Invoices::paid_invoice');
    $routes->get('/invoices/paid/get', 'Invoices::get_paid_invoice');
    
    $routes->get('/invoices/get-products', 'Invoices::get_airport_products');
    $routes->get('/invoices/download', 'Invoices::download_csv');
    $routes->get('/invoices/get_acairport_websites', 'Invoices::get_acairport_websites');

    //////////////////////////////////////////////////////////////////////////////
    $routes->get('/bookings/add', 'Booking::index');
    $routes->get('/create_booking2', 'Booking::create_booking2');
    $routes->post('/create_booking3', 'Booking::create_booking3');
    $routes->post('/create_booking4', 'Booking::create_booking4');
    $routes->post('/bookings/save', 'Booking::create_booking3');

    $routes->get('/reports/bookings/capacity', 'Booking::bookings_capacity');

    // $routes->post('/booking/capacity/report', 'Booking::bookings_capacity_report');
    $routes->get('/booking/capacity/report', 'Booking::bookings_capacity_report');
    

    $routes->get('/bookings', 'Booking::bookings');
    $routes->get('/bookings/report', 'Booking::bookings_report');
    $routes->get('/bookings/view', 'Booking::booking_report_view');
    $routes->get('/bookings/supplier/view', 'Booking::booking_report_view_supplier');
    $routes->get('/bookings/report/supplier', 'Booking::bookings_report_supplier');
    $routes->get('/bookings/driver/view', 'Booking::booking_report_view_driver');
    $routes->get('/bookings/driver/viewb', 'Booking::booking_report_view_driverb');
    $routes->get('/bookings/driver/report', 'Booking::bookings_report_driver');
    $routes->get('/bookings/driver/reportb', 'Booking::bookings_report_driverb');
    $routes->get('/prices', 'Booking::booking_prices');
    $routes->get('/prices/get', 'Booking::booking_prices_get');


    $routes->get('/bookings/details', 'Booking::details');
    $routes->get('/bookings/print_card', 'Booking::print_card');
    $routes->get('/bookings/print_card_new', 'Booking::print_card_new');    
    $routes->get('/bookings/print_dards', 'Booking::print_dards');
    $routes->get('/bookings/print_cards_new', 'Booking::print_cards_new');    
    $routes->get('/bookings/update_status', 'Booking::update_status');
    $routes->get('/bookings/get_record', 'Booking::get_record');
    $routes->post('/bookings/cancel_booking', 'Booking::cancel_booking');
    $routes->post('/bookings/make_refund', 'Booking::make_refund');
    $routes->post('/bookings/edit_booking', 'Booking::edit_booking');
    $routes->get('/bookings/update_move_booking', 'Booking::update_move_booking');
    $routes->get('/bookings/booking_pdf', 'Booking::booking_pdf');
    $routes->get('/bookings/show_status', 'Booking::show_status');
    $routes->get('/bookings/update_source', 'Booking::update_source');
    $routes->get('/bookings/update_note', 'Booking::update_note');
    $routes->get('/bookings/get_airport_websites', 'Booking::get_airport_websites');
    $routes->get('/bookings/get_drivers', 'Booking::get_drivers');
    $routes->get('/bookings/mark_collected', 'Booking::mark_collected');
    $routes->get('/bookings/print_collected', 'Booking::print_collected_slip');
    $routes->get('/bookings/get_customer_history', 'Booking::get_customer_history');

    // $routes->get('/bookings/upload', 'Booking::upload');
    $routes->post('/bookings/upload', 'Booking::upload');

    $routes->get('/domains', 'Domains::index');
    $routes->get('/domains/get', 'Domains::get');
    $routes->get('/domains/add', 'Domains::add');
    $routes->get('/domains/edit', 'Domains::edit');
    $routes->get('/domains/delete_record', 'Domains::delete_record');
    $routes->post('/domains/save','Domains::save');
    $routes->post('/domains/update','Domains::update');
    $routes->get('/domains/duplicate', 'Domains::duplicate');
    

    //////////////////Agents////////////////////////////////

    $routes->get('/promotion/agent', 'Agents::index');
    $routes->get('/agent/get', 'Agents::get');    
    $routes->get('/agent/record', 'Agents::record');  
    $routes->get('/agent/delete_agent', 'Agents::delete_agent');
   $routes->post('/agent/save', 'Agents::save');
    $routes->post('/agent/update', 'Agents::update');


    /////////////////////////////// promotion ///////////////////////
    $routes->get('/promotion/add', 'promotion::index');
    // $routes->post('/promotion/agent', 'promotion::promotion_agent');
    // $routes->get('/promotion/agent', 'promotion::promotion_agent');
    $routes->post('/promotion/save', 'promotion::promotion_save');
    $routes->get('/promotion/view', 'promotion::view_promotions');
    $routes->get('/promotion/get', 'promotion::get_promotions');
    $routes->get('/promotion/delete_record', 'promotion::promotion_delete_record');


    $routes->get('/promotion/update', 'promotion::promotion_update');
    $routes->post('/promotion/update/values', 'promotion::promotion_update_values');

    $routes->get('/promotion/report', 'promotion::promotion_report');
    $routes->get('/promotion/get_report', 'promotion::get_promotion_report');

    //*************************** Drivers *****************************
    $routes->get('/drivers', 'Driver::index');
    $routes->get('/drivers/get', 'Driver::get');
    $routes->get('/drivers/get_record', 'Driver::get_record');
    $routes->get('/drivers/add', 'Driver::add');
    $routes->get('/drivers/edit', 'Driver::edit');
    $routes->get('/drivers/delete', 'Driver::delete');
    $routes->post('/drivers/save','Driver::save');
    $routes->post('/drivers/update','Driver::update');
    /////////////////////////////// Vehicles ///////////////////////
    $routes->get('/vehicles', 'Vehicles::index');
    $routes->get('vehicles/get_models', 'Vehicles::get_models');
    $routes->get('vehicles/get', 'Vehicles::get', ['as' => 'vehicles_get']);

    $routes->get('/vehicles/get', 'Vehicles::get');
    $routes->get('/vehicles/get_record', 'Vehicles::get_record');
    $routes->get('/vehicles/delete_record', 'Vehicles::delete_record');
    $routes->post('/vehicles/save', 'Vehicles::save');
    $routes->post('/vehicles/update', 'Vehicles::update');

	/////////////////////////////// Company ///////////////////////
    $routes->get('/vehicles/companies', 'Companies::index');
    $routes->get('/vehicles/companies/get', 'Companies::get');
    $routes->get('/vehicles/companies/get_record', 'Companies::get_record');
    $routes->get('/vehicles/companies/delete_record', 'Companies::delete_record');
    $routes->post('/vehicles/companies/save', 'Companies::save');
    $routes->post('/vehicles/companies/update', 'Companies::update');

    /////////////////////////////// Company Model ///////////////////////
    $routes->get('/vehicles/companymodels', 'Companymodels::index');
    $routes->get('/vehicles/companymodels/get', 'Companymodels::get');
    $routes->get('/vehicles/companymodels/get_record', 'Companymodels::get_record');
    $routes->get('/vehicles/companymodels/delete_record', 'Companymodels::delete_record');
    $routes->post('/vehicles/companymodels/save', 'Companymodels::save');
    $routes->post('/vehicles/companymodels/update', 'Companymodels::update');

    /////////////////////////////// Vehicle Color ///////////////////////
    $routes->get('/vehicles/colors', 'Colors::index');
    $routes->get('/vehicles/colors/get', 'Colors::get');
    $routes->get('/vehicles/colors/get_record', 'Colors::get_record');
    $routes->get('/vehicles/colors/delete_record', 'Colors::delete_record');
    $routes->post('/vehicles/colors/save', 'Colors::save');
    $routes->post('/vehicles/colors/update', 'Colors::update');

    /////////////////////////////// Security Guard ///////////////////////
    $routes->get('/security', 'Security::index');
    $routes->get('security/get', 'Security::get');
    $routes->get('security/delete_record', 'Security::delete_record');
    $routes->get('security/get_record', 'Security::get_record'); // fetch single record for edit
    $routes->post('security/save', 'Security::save');  // add new record
    $routes->post('security/update', 'Security::update'); // update record

});
/////////////////////////////// api routes ///////////////////////

$routes->get('/api_create_booking_by_website', 'ApiController::create_booking_by_website');
$routes->get('/api_create_booking2', 'ApiController::create_booking2');
$routes->get('/api_create_oldnewUI_booking', 'ApiController::create_oldNewUI_booking');
$routes->get('/api_create_newUI_booking', 'ApiController::create_newUI_booking');
$routes->get('/api_create_goAirport_booking', 'ApiController::create_goAirport_booking');

$routes->get('/api_create_booking3', 'ApiController::create_booking3');
$routes->get('/send_mail_api', 'ApiController::send_mail_api');
$routes->get('/update_booking_status_api', 'ApiController::update_booking_status_api');
$routes->get('/send_contact_us', 'ApiController::send_contact_us');
$routes->get('/get_domain', 'ApiController::get_domain');
$routes->get('/sagepay', 'ApiController::sagepay');
$routes->get('/checkstatusapi', 'ApiController::checkStatus');
$routes->get('/api_get_random_strings', 'ApiController::get_random_strings');
$routes->get('/api_get_go_booking', 'ApiController::get_go_booking');
$routes->get('/api_update_go_booking', 'ApiController::update_go_booking');
// $routes->get('/api_get_single_booking', 'ApiController::get_single_booking'); 

$routes->get('/api_get_product', 'ApiController::get_product');
$routes->get('/api_get_product_addons', 'ApiController::get_product_addons');
$routes->get('/api_get_addon_price', 'ApiController::get_addon_price');
$routes->get('/api_subscriber', 'ApiController::subscriber');

////////////////CarBooking Api///////////
$routes->group('api', function($routes){
    $routes->get('carbooking/models/(:num)', 'CarBookingAPI::get_models/$1'); // Get models by make
    $routes->get('carbooking/colors', 'CarBookingAPI::get_colors'); // Get colors by make+model
    $routes->get('carbooking/car', 'CarBookingAPI::get_car'); // Get single car by make+model+color   
    $routes->get('carbooking/cars', 'CarBookingAPI::get_cars'); // optional
    $routes->post('carbooking/calculate_total', 'CarBookingAPI::calculate_total_proxy'); // Calculate total booking price
$routes->get('carbooking/guard_price', 'CarBookingAPI::get_guard_price');
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
