<?php
//header('Access-Control-Allow-Origin: https://assetlite.banglalink.net');
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
   // return view('welcome');
    abort('403');
});

//Route::get( '/{path?}', function(){
//    return view('index');
//} )->where('path', '.*');



Route::get('{bannerType}/{model}/{fileName}', 'API\V1\ImageFileViewerController@bannerImage');
//Route::get('banner-mobile/{model}/{fileName}', 'API\V1\ImageFileViewerController@bannerImageMobile');
//Route::get('other/{model}/{fileName}', 'API\V1\ImageFileViewerController@bannerImageWeb');

