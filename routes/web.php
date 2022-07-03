<?php

use App\Http\Controllers\HomeController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'checksession'], function () {
  Route::get('/', 'Auth\LoginController@login')->name('login');
  Route::get('/login', 'Auth\LoginController@login')->name('login');
  Route::post('/processing-login', 'Auth\LoginController@checking_login')->name('login.check');
  Route::get('/lupa-password', 'HomeController@forget_password')->name('forgetPassword');
  Route::get('/lupa-password/berhasil', 'HomeController@after_forget')->name('berhasil');
  Route::post('/processing-lupa-pwd', 'HomeController@checking_lupa')->name('forgetpwd.check');
  Route::post('/update-password', 'Auth\ResetPasswordController@update_password')->name('update.password');
});

Route::group(['middleware' => 'checkauth'], function () {
  Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

  //dashboard
  Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
  
  //layouting
  Route::group(['prefix' => 'layouting'], function () {
    Route::get('/', 'LayoutingController@index')->name('layouting');
    Route::post('/', 'LayoutingController@ajax')->name('layouting');
    Route::get('/new', 'LayoutingController@index')->name('layouting.new');
    Route::post('/new', 'LayoutingController@create')->name('layouting.new');
    Route::get('/show/{id}', 'LayoutingController@show')->name('layouting.show');
    Route::post('/show/{id}', 'LayoutingController@show_ajax')->name('layouting.show');
  });

  //route digunakan untuk menambahkan module ke sys_role_permissions
  Route::get('/insert-batch/rpermission/{applicationId}/{type}', 'DashboardController@insert_batch')->name('batching');
  Route::get('/insert-batch/permission/{permissionId}', 'DashboardController@insert_batch_permission')->name('batching-permission');
});
