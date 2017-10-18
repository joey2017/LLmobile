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

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

// Route::get('/', 'HomeController@index');
Route::get('/', 'BizController@index');
Route::get('/biz/entrance', 'BizController@entrance');
Route::get('/biz/login', 'BizController@login');
Route::get('/biz/ajax_login', function() {
  return View::make('/ajax_login');
});
Route::post('/biz/ajax_login', 'BizController@ajax_login');
Route::get('/home', 'HomeController@index');

// Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function() {
//     Route::get('/', 'HomeController@index');
// });

Auth::routes();

// Route::get('/home', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');
