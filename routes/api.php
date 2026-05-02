<?php

use Illuminate\Support\Facades\Route;
use Module\Customer\Access\Controller\CustomerController;
use Module\Offer\Access\Controller\OfferAdminController;
use Module\Offer\Access\Controller\OfferController;
use Module\Shared\Middleware\JwtAuth;
use Module\Store\Access\Controller\StoreController;
use Module\User\Access\Controller\UserAdminController;


//
// mobile
//

Route::post('/customer/login', [CustomerController::class, 'login']);

Route::group(['middleware' => [JwtAuth::class]], function () {

    Route::post('/customer/change-store', [CustomerController::class, 'changeStore']);

    Route::get('/offer/list', [OfferController::class, 'list']);
    Route::get('/offer/{id}', [OfferController::class, 'details'])->whereNumber('id');

    Route::get('/store/list', [StoreController::class, 'list']);
    Route::get('/store/{id}', [StoreController::class, 'details'])->whereNumber('id');

});


//
// CMS
//

Route::post('/admin/user/login', [UserAdminController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/admin/offer/filters', [OfferAdminController::class, 'filters']);
    Route::get('/admin/offer/list', [OfferAdminController::class, 'list']);
    Route::get('/admin/offer/{id}', [OfferAdminController::class, 'details'])->whereNumber('id');
    Route::post('/admin/offer/change-visibility', [OfferAdminController::class, 'changeVisibility']);

});
