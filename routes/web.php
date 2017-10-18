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
Route::get('/purchase/home', 'PurchaseController@home');
Route::get('/purchase/ajax_get_qualitygoods', 'PurchaseController@ajax_get_qualitygoods');
Route::get('/purchase/index', 'PurchaseController@index');
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

