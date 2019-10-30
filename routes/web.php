<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('upload','TesController@tesvideo');
Route::get('upload-story','TesController@tesStory');

Route::get('verifyemail/{cryptedcode}', 'LandingPageController@verifyEmail');
Route::get('resend-email-activation', 'LandingPageController@resendEmailActivation');
//Route::get('tes-email','User\HomeController@tes_email');

if(env('APP_PROJECT')=='Celebgramme'){
  Route::get('prices', 'Auth\RegisterController@ordersg');
  Route::post('orderslogin', 'Auth\RegisterController@prologinorder');
  // Route::get('prices', 'Auth\RegisterController@showPrices');
  Route::get('checkout', 'Auth\RegisterController@showCheckout');
  Route::post('checkout', 'Auth\RegisterController@process_checkout');
}
Route::get('test', 'Auth\LoginController@test');
Route::get('image-editor-pixie', 'User\ResearchController@image_editor_index_pixie');

Route::get('test', 'User\AccountController@test');

/* IDAFF */
Route::get('postback-idaff', 'LandingPageController@post_back_idaff');

/* Cron */
Route::get('update-publish-schedule-369', 'LandingPageController@update_publish_schedule');
Route::get('update-cookie-account-369', 'LandingPageController@update_cookie_account');

Route::get('order-kupon','User\OrderController@orderkupon');

//buat fixing error 
Route::get('refresh-global','Admin\AccountController@refresh_global');

// Auth::routes();
Route::auth();
Route::group(['middleware' => 'auth'], function () {
  // Route::get('/', 'User\HomeController@index');
  //Route::get('/', 'User\AccountController@index');
  Route::get('/','Admin\UserController@index');
  Route::get('home','Admin\UserController@index');
  
  /*
  * ADMIN 
  */
  Route::get('list-user', 'Admin\UserController@listuser');
  Route::post('submit-refund', 'Admin\UserController@submit_refund');
  Route::get('list-user-affiliate', 'Admin\UserController@listuseraffiliate');
  Route::get('list-user-refund', 'Admin\UserController@user_refund');
  Route::get('list-admin', 'Admin\UserController@listadmin');
  Route::get('search-user', 'Admin\UserController@search');
  Route::get('search-affiliate', 'Admin\UserController@searchaffiliate');
  Route::get('search-refund', 'Admin\UserController@searchrefund');
  Route::get('search-admin', 'Admin\UserController@searchadmin');

  Route::get('show-addadmin', 'Admin\UserController@show_addadmin');
  Route::post('add-admin', 'Admin\UserController@addadmin');
  Route::get('show-log','Admin\UserController@show_log');

  Route::get('action','Admin\UserController@show');
  Route::get('show-edit','Admin\UserController@showedit');
  Route::post('update-time', 'Admin\UserController@updatetime');
  Route::get('account/delete/', 'Admin\UserController@delete');
  Route::get('show-max-account', 'Admin\UserController@showmaxaccount');
  Route::post('update-max-account', 'Admin\UserController@updatemaxaccount');
  Route::get('show-email', 'Admin\UserController@showemail');
  Route::post('update-email', 'Admin\UserController@updateemail');
  Route::get('login-user', 'Admin\UserController@loginuser');
  Route::get('show-time-log', 'Admin\UserController@showtimelog');
  
  Route::get('list-account', 'Admin\AccountController@index');
  Route::get('search-eacount', 'Admin\AccountController@searchacc');
  Route::get('peraccount/delete/', 'Admin\AccountController@delaccount');
  Route::get('peraccount/check-login/', 'Admin\AccountController@check_login');
  Route::get('check-login-ig','Admin\AccountController@check_login_ig');
  Route::get('process-valid-account','Admin\AccountController@process_valid_account');

  Route::get('schedules', 'Admin\ScheduleController@index');
  Route::get('view-schedule-account', 'Admin\ScheduleController@schedulaccount');
  Route::get('search-schedule', 'Admin\ScheduleController@searcschedul');

  Route::get('list-order', 'Admin\OrderController@index');
  //Route::get('search-orders','Admin\OrderController@searchorder');
  Route::get('search-orders','Admin\OrderController@searchorder');
  Route::get('delete-order', 'Admin\OrderController@deleteorder');
  Route::post('proses-del-order', 'Admin\OrderController@prosesdelorder');
  Route::get('confir-order', 'Admin\OrderController@confirorders');
  Route::post('proses-confir-order', 'Admin\OrderController@prosesconfir');
  //Route::post('/crud/update', 'CrudController@update');
  //Route::post('update', 'Admin\HomeController@update');
  Route::get('visat', 'Admin\HomeController@viatt');
  Route::post('importExcel', 'Admin\HomeController@importxls');
  Route::get('coupon','Admin\CouponController@index');
  Route::post('addcoupon','Admin\CouponController@addusercoupon');
  Route::get('lisusercoupon','Admin\CouponController@listuser');
  Route::post('generatecoupon', 'Admin\CouponController@generatekupon');
  Route::get('gencoupon', 'Admin\CouponController@generatecron');
  //Route::get('expirekupons', 'Admin\CouponController@expirekupon');

  
  
  /*
  * User 
  */
    // Profile
  Route::get('change-password', function () {
      return view('profile.index');
  });
  Route::post('profile/update', 'User\ProfileController@update');
  
  // Account
  Route::get('account', 'User\AccountController@index');
  Route::get('account-test', 'User\AccountController@index_test');
  Route::post('account/chklogin', 'User\AccountController@chklogin');
  Route::post('account/edit-password', 'User\AccountController@edit_password');
  Route::get('account/delete/{id}', 'User\AccountController@delete');
  Route::post('account/call-action', 'User\AccountController@call_action');
  Route::post('account/call-action-all', 'User\AccountController@call_action_all');
  Route::post('account/post-berurutan', 'User\AccountController@post_berurutan');
  
  // Schedule - photo
  Route::get('schedule', 'User\ScheduleController@index');
  Route::get('load-main-schedule', 'User\ScheduleController@load_main_schedule');
  Route::post('schedule/publish', 'User\ScheduleController@publish');
  Route::post('schedule/saveimage', 'User\ScheduleController@saveimage');
  Route::get('schedule/add', 'User\ScheduleController@add');
  Route::get('schedule/repost/{imageid}', 'User\ScheduleController@repost');
  Route::get('schedule/edit/{sid}', 'User\ScheduleController@add');
  Route::get('schedule/delete/{id}', 'User\ScheduleController@delete');
  Route::get('load-schedule-list', 'User\ScheduleController@load_schedule');
  Route::get('pagination-schedule-list', 'User\ScheduleController@pagination_schedule');
  Route::post('schedule/call-action-start-schedule-akun', 'User\ScheduleController@call_action_start_schedule_akun');
  
  //Schedule video new
  Route::get('schedule/video', 'User\ScheduleController@schedule_video');
  Route::post('schedule/save-video', 'User\ScheduleController@save_video_schedule');
  Route::post('schedule/publish-video', 'User\ScheduleController@publish_video_schedule');
  Route::get('schedule/edit-video/{sid}', 'User\ScheduleController@schedule_video');

  // Schedule story 
  Route::get('schedule/story', 'User\ScheduleController@schedule_story');
  Route::post('schedule/save-story', 'User\ScheduleController@save_story_schedule');
  Route::post('schedule/publish-story', 'User\ScheduleController@publish_story_schedule');
  Route::get('schedule/edit-story/{sid}', 'User\ScheduleController@schedule_story');

  // Schedule - video
  Route::get('schedule-video', 'User\ScheduleController@index_video');
  Route::post('schedule-video/publish', 'User\ScheduleController@publish_video');
  Route::post('schedule-video/save', 'User\ScheduleController@savevideo');
  Route::get('schedule-video/add', 'User\ScheduleController@add_video');
  Route::get('schedule-video/delete/{id}', 'User\ScheduleController@delete_video');

  //search hashtags
  Route::get('search-hashtags', 'User\HomeController@research');
  Route::post('process-search-hashtags', 'User\ResearchController@search_hashtags');
  Route::post('submit-hashtags', 'User\ResearchController@submit_hashtags');
  Route::post('delete-hashtags', 'User\ResearchController@delete_hashtags');
  
  //caption
  Route::get('caption', 'User\HomeController@caption');
  Route::post('submit-caption', 'User\CaptionController@submit_caption');
  Route::post('delete-caption', 'User\CaptionController@delete_caption');
  
  //Images
  Route::get('saved-images', 'User\HomeController@saved_images');
  Route::post('save-image', 'User\ImageController@save_image');
  Route::get('save-temp-image', 'User\ImageController@save_temp_image');
  Route::post('delete-image', 'User\ImageController@delete_image');
  Route::post('save-image-schedule', 'User\ImageController@save_image_schedule');
  Route::post('multiple-upload', 'User\ImageController@multiple_upload');
  Route::get('load-image-list', 'User\ImageController@load_images');
  Route::get('pagination-image-list', 'User\ImageController@pagination_images');
  
  //hashtags + image handle
  Route::get('show-photo-hashtags/{input_hashtags}', 'User\ResearchController@show_photo_hashtags');
  Route::post('more-photo', 'User\ResearchController@more_photo');
  Route::get('image-editor', 'User\ResearchController@image_editor_index');
  Route::get('makeimage', 'User\ScheduleController@makeimage');
  Route::post('save-image-IG', 'User\ResearchController@save_image_IG');
  
  // Maintenance
  Route::get('maintenance', 'User\MaintenanceController@maintenance');
  Route::get('maintenance/clearcache', 'User\MaintenanceController@clearcache');
  Route::get('maintenance/clearview', 'User\MaintenanceController@clearview');
  Route::get('maintenance/clearroute', 'User\MaintenanceController@clearroute');
  Route::get('maintenance/clearconfig', 'User\MaintenanceController@clearconfig');
  Route::get('maintenance/optimize', 'User\MaintenanceController@optimize');
  Route::get('maintenance/delsche', 'User\MaintenanceController@delsche');

  //Search IG
  Route::get('searchig', 'User\SearchController@index')->name('searchig');
  Route::post('igdata','User\SearchController@getData')->name('igdata');
  Route::get('ctest','User\SearchController@checkCache')->name('ctest');
  Route::get('insightigdata/{user_id}', 'User\SearchController@getDataInsight')->name('insightigdata');

  Route::get('testdata','User\SearchController@getDataAPI');

  if(env('APP_PROJECT')=='Celebgramme'){
    Route::get('order', 'User\OrderController@index');
    Route::post('add-order', 'User\OrderController@addorder');
    Route::get('confir-payment', 'User\OrderController@confirpay');
    Route::post('prose-con-pay', 'User\OrderController@proconpay');
    Route::get('check-no-order', 'User\OrderController@checknoorder');
    Route::get('confirm-order', 'User\OrderController@listorderuser');
    Route::get('confirm-payment', 'User\OrderController@index_confirm_payment');
  }
  //Route::get('orders', 'User\OrderController@orderg');
  //Route::post('search-orders','Admin\OrderController@searchorder');
  
  Route::get('test-image', 'User\ScheduleController@test_image');
});

/* Middleware admin */
Route::group(['middleware' => ['web','auth','admin']], function() {
  Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');  
});

/* Middleware API */
Route::group(['middleware' => ['APIMiddleware']], function() {
  Route::get('/post-ig','APIController@post_ig');
  Route::get('/delete-post-ig','APIController@delete_post_ig');
});


