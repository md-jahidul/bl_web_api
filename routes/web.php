<?php
header('Access-Control-Allow-Origin: http://172.16.8.160:9443');
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

Route::get('/', function () {
    return view('welcome');
});

//Route::get( '/{path?}', function(){
//    return view('index');
//} )->where('path', '.*');

