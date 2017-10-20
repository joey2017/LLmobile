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

Auth::routes();

Route::get('/', 'BizController@index');
Route::get('/biz/entrance', 'BizController@entrance');
Route::get('/biz/login', 'BizController@login');
Route::get('/biz/shop_count', 'BizController@shop_count');
Route::get('/biz/entrance_more/{id}', 'BizController@entrance_more');
Route::get('/purchase/home', 'PurchaseController@home');
// Route::get('/purchase/detail/', 'PurchaseController@detail');
Route::get('/purchase/detail/{id}','PurchaseController@detail');
Route::get('/purchase/search','PurchaseController@search');
Route::post('/purchase/add_card','PurchaseController@add_card');
Route::get('/purchase/cart','PurchaseController@cart');
Route::get('/purchase/check_order','PurchaseController@check_order');
Route::get('/purchase/order','PurchaseController@order');
Route::get('/pay/purchase_go_pay','PayController@purchase_go_pay');
Route::post('/purchase/create_order','PurchaseController@create_order');
Route::get('/purchase/ajax_get_qualitygoods', 'PurchaseController@ajax_get_qualitygoods');
<<<<<<< HEAD
Route::get('/purchase/index', 'PurchaseController@index');
Route::get('/purchase', 'PurchaseController@index');
=======
Route::get('/purchase/index/{t?}', 'PurchaseController@index');
>>>>>>> 003c6168160d8664757529ea542e32e4100b8efa
/*Route::get('/biz/ajax_login', function() {
  return View::make('/ajax_login');
});*/
Route::post('/biz/ajax_login', 'BizController@ajax_login');
Route::get('/purchase/ajax_get_goods', 'PurchaseController@ajax_get_goods');
Route::get('/purchase/ajax_get_attr', 'PurchaseController@ajax_get_attr');
Route::get('/home', 'HomeController@index');

// Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function() {
//     Route::get('/', 'HomeController@index');
// });

Route::get('/home', 'HomeController@index');

