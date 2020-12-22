<?php

use Illuminate\Support\Facades\Route;

Route::prefix('collections')->group(function () {
    Route::get('/', 'CollectionsController@index');
    Route::get('/{id}', 'CollectionsController@show');
    Route::post('/', 'CollectionsController@create');
    Route::post('/{id}/toggle', 'CollectionsController@toggle');
    Route::patch('/{id}', 'CollectionsController@update');
    Route::post('/{id}/designs', 'CollectionsController@addDesign');
    Route::delete('/{id}/designs/{designId}', 'CollectionsController@removeDesign');
    Route::post('/{id}/moodboards', 'CollectionsController@addMoodboard');
    Route::delete('/{id}/moodboards/{moodboardId}', 'CollectionsController@deleteMoodboard');
});

Route::prefix('design')->group(function () {
    Route::get('/', 'DesignController@index');
    Route::get('/{id}', 'DesignController@show');
    Route::post('/', 'DesignController@create');
    Route::patch('/{id}', 'DesignController@update');
    Route::post('/{id}/image', 'DesignController@updateImage');
    Route::post('/{id}/file', 'DesignController@updateFile');
});

Route::prefix('seasons')->group(function () {
    Route::get('/', 'SeasonsController@index');
    Route::get('/{id}', 'SeasonsController@show');
    Route::post('/', 'SeasonsController@create');
    Route::patch('/{id}', 'SeasonsController@update');
    Route::delete('/{id}', 'SeasonsController@delete');
});

Route::prefix('tags')->group(function () {
    Route::get('/', 'TagsController@index');
    Route::get('/{id}', 'TagsController@show');
    Route::post('/', 'TagsController@create');
    Route::patch('/{id}', 'TagsController@update');
    Route::delete('/{id}', 'TagsController@delete');
});

Route::prefix('categories')->group(function () {
    Route::get('/', 'CategoriesController@index');
    Route::get('/{id}', 'CategoriesController@show');
    Route::post('/', 'CategoriesController@create');
    Route::patch('/{id}', 'CategoriesController@update');
    Route::delete('/{id}', 'CategoriesController@delete');
});

Route::prefix('libCategories')->group(function () {
    Route::get('/', 'CategoriesController@libIndex');
});

Route::prefix('colors')->group(function () {
    Route::get('/', 'ColorsController@index');
    Route::get('/{id}', 'ColorsController@show');
    Route::post('/', 'ColorsController@create');
    Route::patch('/{id}', 'ColorsController@update');
    Route::delete('/{id}', 'ColorsController@delete');
});

Route::prefix('goods')->group(function () {
    Route::get('/masks', 'GoodsController@indexGoods');
    Route::get('/masks/{id}', 'GoodsController@showGood');
    Route::post('/masks', 'GoodsController@storeGood');
    Route::post('/masks/{id}/image', 'GoodsController@updateGoodImage');
    Route::patch('/masks/{id}', 'GoodsController@updateGood');
    Route::delete('/masks/{id}', 'GoodsController@deleteGood');

    Route::get('/requests', 'GoodsController@inexRequests');
    Route::get('/requests/{id}', 'GoodsController@showRequest');
    Route::post('/requests/{id}/approve', 'GoodsController@approveRequest');
    Route::post('/requests/{id}/reject', 'GoodsController@rejectRequest');
});

Route::prefix('requests')->group(function () {
    Route::get('collections', 'RequestsController@indexCollections');
    Route::get('collections/{id}', 'RequestsController@showCollection');
    Route::post('collections/{id}/reject', 'RequestsController@rejectCollection');
    Route::post('collections/{id}/approve', 'RequestsController@approveCollection');

    Route::get('exclusives', 'RequestsController@indexExclusive');
    Route::get('exclusives/{id}', 'RequestsController@showExclusive');
    Route::post('exclusives/{id}/reject', 'RequestsController@rejectExclusive');
    Route::post('exclusives/{id}/approve', 'RequestsController@approveExclusive');
});