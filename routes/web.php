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

// $router->get('storage/{filename}', function ($filename)
// {
//     $path = storage_path('public/images' . $filename);

//     // if (!File::exists($path)) {
//     //     abort(404);
//     // }

//     $file = File::get($path);
//     $type = File::mimeType($path);

//     $response = Response::make($file, 200);
//     $response->header("Content-Type", $type);

//     return $response;
// });



$router->get('/key', function() {
    return str_random(32);
});

$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');

$router->get('/item', 'ItemController@index');
$router->get('/item/get/random', 'ItemController@get_random');
$router->get('/item/get/by_store/{slug}', 'ItemController@get_by_store');
$router->get('/item/{slug}', 'ItemController@show');
$router->get('/item/price/{id}', 'ItemController@item_price');

$router->get('/store', 'StoreController@index');
$router->get('/store/get/random', 'StoreController@get_random');
$router->get('/store/{slug}', 'StoreController@show');


// $router->group(['prefix' => 'file'], function () use ($router) {
//     $router->post('/update_pic_item/{id}', 'FileController@update_pic_item');
//     // $router->get('/read_file', 'FileController@read_file');
//     // $router->get('/download_file', 'FileController@download_file');
//     // $router->get('/write_file', 'FileController@write_file');
// });




$router->group(['middleware' => 'jwt.auth'], function () use ($router) {

    $router->post('/logout', 'AuthController@logout');
    // $router->post('/refresh', 'AuthController@refresh');
    $router->get('/me', 'AuthController@me');

    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->get('/{id}', 'UserController@show');
        $router->post('/', 'UserController@store');
        $router->put('/{id}', 'UserController@update');
        $router->delete('/{id}', 'UserController@destroy');
        $router->post('/{id}/restore', 'UserController@restore');
        $router->put('/change_password/{id}', 'UserController@change_password');
    });

    $router->group(['prefix' => 'file'], function () use ($router) {
        $router->post('/update_pic_item/{id}', 'FileController@update_pic_item');
        // $router->get('/read_file', 'FileController@read_file');
        // $router->get('/download_file', 'FileController@download_file');
        // $router->get('/write_file', 'FileController@write_file');
    });

    $router->group(['prefix' => 'item'], function () use ($router) {
        // $router->get('/', 'ItemController@index');
        // $router->get('/get/random', 'ItemController@get_random');
        // $router->get('/get/by_store/{$slug}', 'ItemController@get_by_store');
        $router->get('/get/my_item', 'ItemController@get_my_item');
        // $router->get('/{slug}', 'ItemController@show');
        // $router->get('/price/{id}', 'ItemController@item_price');
        $router->post('/', 'ItemController@store');
        $router->post('/{id}', 'ItemController@update');
        $router->post('/update_price/{id}', 'ItemController@update_price');
        $router->delete('/{id}', 'ItemController@destroy');
        $router->post('/{id}/restore', 'ItemController@restore');

        // buat fungsi tambah & ubah harga barang
    });

    $router->group(['prefix' => 'store'], function () use ($router) {
        // $router->get('/', 'StoreController@index');
        // $router->get('/get/random', 'StoreController@get_random');
        $router->get('/get/my_store', 'StoreController@get_my_store');
        // $router->get('/{slug}', 'StoreController@show');
        $router->post('/', 'StoreController@store');
        $router->put('/{id}', 'StoreController@update');
        $router->delete('/{id}', 'StoreController@destroy');
        $router->post('/{id}/restore', 'StoreController@restore');
    });



});
