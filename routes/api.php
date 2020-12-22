<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->prefix('auth')->group(function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', 'AuthController@me');
        Route::post('/logout', 'AuthController@logout');
    });
    Route::get('/verify/{id}', 'VerificationController@verify')->name('verification.verifyemail');
    Route::post('/verify/resend', 'VerificationController@resend');
    Route::post('/forgot', 'ForgotPasswordController@sendResetLinkEmail');
    Route::get('/reset', 'ForgotPasswordController@showReset')->name('forgotpassword.showreset');
    Route::post('/reset', 'ForgotPasswordController@reset');
});

Route::prefix('app')->group(function () {
    Route::get('/init', 'AppController@init');
});

Route::prefix('search')->group(function () {
    Route::get('/suggestions/feed', 'SearchController@feedSuggestions');
    Route::get('/suggestions/tag', 'SearchController@tagSuggestions');
});

Route::prefix('media')->namespace('Media')->group(function () {
    Route::get('/preview/{code}', 'MediaController@preview');
});

// For CloudFlare caching
Route::prefix('v1/image')->group(function () {
    Route::get('detail/design/{code}', 'ImageController@detail');
    Route::prefix('thumbnail/design')->group(function () {
        Route::get('/{code}', 'ImageController@thumbnail');
        Route::get('/{code}/tiny', 'ImageController@tinyThumbnail');
        Route::get('/{code}/large', 'ImageController@largeThumbnail');
    });
});

Route::middleware('auth:api')->group(function () {
    Route::post('/token/generate', 'TokenController@generate');
});

Route::get('/download/{code}', 'DownloadController@download');

Route::prefix('countries')->group(function () {
    Route::get('/', 'CountriesController@index');
    Route::get('/{id}', 'CountriesController@show');
});

Route::prefix('users/{id}')->middleware('selfUser')->group(function () {
    Route::patch('/', 'UsersController@update');
    Route::patch('/updatePassword', 'UsersController@updatePassword');
    Route::get('/quota', 'UsersController@showQuota');
    Route::get('/payments', 'UsersController@showPayments');
    Route::patch('/lang-pref', 'UsersController@lang_pref');
});

Route::prefix('pages')->group(function () {
    Route::get('/{slug}', 'PagesController@show');
    Route::get('/{slug}/blocks', 'PagesController@showBlocks');
});

Route::prefix('pricing')->group(function () {
    Route::get('/', 'PricingController@index');
});

Route::prefix('requests')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/collection', 'RequestsController@collection');
        Route::get('/collection/hasPending', 'RequestsController@hasPending');
        Route::get('/exclusive', 'RequestsController@exclusive');
        Route::post('/', 'RequestsController@store');

        Route::post('/exclusive', 'RequestsController@storeExclusive');
        Route::get('/exclusive/check', 'RequestsController@get_exclusive');
        Route::delete('/exclusive/{id}/delete-reject', 'RequestsController@deleteExclusiveRejected');
        Route::delete('collection/{id}/delete', 'RequestsController@deleteCollection');
    });
});

Route::prefix('posts')->group(function () {
    Route::get('/', 'PostsController@index');
    Route::get('/{id}', 'PostsController@show');
});

Route::prefix('list')->group(function () {
    Route::get('/view/{token}', 'ListController@viewByToken');
    Route::middleware('auth:api')->group(function () {
        Route::post('/', 'ListController@create');
        Route::post('/{id}', 'ListController@addProduct');
        Route::delete('/{listId}/design/{designId}', 'ListController@removeProduct');
        Route::delete('/{id}', 'ListController@delete');
        Route::post('/share/{id}', 'ListController@share');
        Route::patch('/{id}', 'ListController@update');
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('payments')->group(function () {
        Route::get('/', 'Payments\PaymentController@index');
        Route::prefix('braintree')->group(function () {
            Route::get('token', 'Payments\BrainTreeController@token');
        });
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('quota')->group(function () {
        Route::post('/buy', 'QuotaController@buy');
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('downloads')->group(function () {
        Route::get('/', 'DownloadController@getUserDownloads');
        Route::get('/numberOfDownloads', 'DownloadController@getNumberOfDownloads');
        Route::get('/{code}/{pkg}', 'DownloadController@downloadDetails');
    });
});

Route::prefix('addresses')->middleware('auth:api')->group(function () {
    Route::get('/', 'AddressController@index');
    Route::post('/', 'AddressController@store');
    Route::patch('/{id}', 'AddressController@update');
    Route::delete('/{id}', 'AddressController@delete');
});

Route::prefix('goods')->group(function () {
    Route::get('/', 'GoodsController@index');
    Route::middleware('auth:api')->group(function () {
        Route::post('/request', 'GoodsController@store');
        Route::delete('/request/{id}', 'GoodsController@delete');
    });
});

Route::prefix('collections')->group(function () {
    Route::get('/', 'CollectionsController@index');
    Route::get('/{id}', 'CollectionsController@show');
});

Route::prefix('design')->group(function () {
    Route::get('/', 'DesignController@index');
    Route::get('/{code}', 'DesignController@show');
    Route::get('/{id}/tags', 'DesignController@tags');
});
