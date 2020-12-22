<?php

use Illuminate\Support\Facades\Route;

Route::prefix('upload')->group(function () {
    Route::prefix('local')->group(function () {
        Route::post('/image', 'UploadController@localImage');
    });
});

Route::prefix('countries')->group(function () {
    Route::get('/', 'CountriesController@index');
    Route::get('/{id}', 'CountriesController@show');
    Route::post('/', 'CountriesController@create');
    Route::patch('/{id}', 'CountriesController@update');
    Route::delete('/{id}', 'CountriesController@delete');
});

Route::prefix('coupons')->group(function () {
    Route::get('/', 'CouponsController@index');
    Route::get('/{id}', 'CouponsController@show');
    Route::post('/', 'CouponsController@create');
    Route::patch('/{id}', 'CouponsController@update');
    Route::delete('/{id}', 'CouponsController@delete');
});

Route::prefix('users')->group(function () {
    Route::get('/', 'UsersController@index');
    Route::get('/{id}', 'UsersController@show');
    Route::post('/', 'UsersController@create');
    Route::get('/{id}/quota', 'UsersController@showQuota');
    Route::get('/{id}/payments', 'UsersController@showPayments');
    Route::patch('/{id}/updatePassword', 'UsersController@updatePassword');
    Route::post('/{id}/avatar', 'UsersController@updateAvatar');
    Route::patch('/{id}/updateQuota', 'UsersController@updateQuota');
    Route::patch('/{id}/toggleCreatorPrivileges', 'UsersController@toggleCreatorPrivileges');
});

Route::prefix('pages')->group(function () {
    Route::get('/', 'PagesController@index');
    Route::get('/{id}', 'PagesController@show');
    Route::patch('/{id}', 'PagesController@update');
    Route::post('/clients', 'PagesController@storeClients');
    Route::get('/{id}/blocks', 'PagesController@showBlocks');
    Route::post('/{id}/blocks', 'PagesController@createBlock');
    Route::patch('/{id}/blocks/{blockId}', 'PagesController@updateBlock');
    Route::post('/{id}/blocks/sort', 'PagesController@sortBlocks');
    Route::delete('/{id}/blocks/{blockId}', 'PagesController@deleteBlock');
});

Route::prefix('pricing')->group(function () {
    Route::get('/', 'PricingController@index');
    Route::patch('/', 'PricingController@update');
});

Route::prefix('posts')->group(function () {
    Route::get('/', 'PostsController@index');
    Route::get('/{id}', 'PostsController@show');
    Route::post('/', 'PostsController@create');
    Route::patch('/{id}', 'PostsController@update');
    Route::delete('/{id}', 'PostsController@delete');
});
