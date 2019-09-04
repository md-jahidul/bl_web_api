<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/key', function() {
    return str_random(32);
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('menu','API\V1\MenuApiController@getMenu');
    $router->get('header-footer','API\V1\FooterMenuApiController@getFooterMenu');
    $router->get('slider','DemoApiController@slider');
    $router->get('footer','DemoApiController@footer');
});
