<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/register",'App\Http\Controllers\Authentication@register');
Route::post("/login",'App\Http\Controllers\Authentication@login');
Route::post("/profilecreation",'App\Http\Controllers\Authentication@profilecreation');
Route::post("/getstarted",'App\Http\Controllers\Authentication@getstarted');
Route::post("/emailverification",'App\Http\Controllers\Authentication@emailverification');

//user
Route::post('/userupdate', 'App\Http\Controllers\User@userupdate');
Route::post('/insertReview', 'App\Http\Controllers\User@insertReview');

Route::post('/insertContact', 'App\Http\Controllers\User@insertContact');
Route::post('/insertWishlist', 'App\Http\Controllers\User@insertWishlist');
Route::post('/addItemCart', 'App\Http\Controllers\User@addItemCart');
Route::post('/addItemCartCoupon', 'App\Http\Controllers\User@addItemCartCoupon');


Route::post('/removeItemCart', 'App\Http\Controllers\User@removeItemCart');
Route::post('/insertSubscriber', 'App\Http\Controllers\User@insertSubscriber');
Route::post('/cartPaymentFree', 'App\Http\Controllers\User@cartPaymentFree');

Route::post('/cartPaymentInitiate', 'App\Http\Controllers\User@cartPaymentInitiate');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');

Route::post('/cartPaymentSucessWebhook', 'App\Http\Controllers\User@cartPaymentSucessWebhook');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');
Route::post('/cartPaymentSucess', 'App\Http\Controllers\User@cartPaymentSucess');

//Admin

Route::post('/insertCategory', 'App\Http\Controllers\Admin@insertCategory');
Route::post('/insertTrainer', 'App\Http\Controllers\Admin@insertTrainer');
Route::post('/insertWorkshop', 'App\Http\Controllers\Admin@insertWorkshop');
Route::post('/insertTestimonial', 'App\Http\Controllers\Admin@insertTestimonial');
Route::post('/insertItem', 'App\Http\Controllers\Admin@insertItem');
Route::post('/insertEvent', 'App\Http\Controllers\Admin@insertEvent');

Route::post('/insertCoupon', 'App\Http\Controllers\Admin@insertCoupon');
Route::post('/insertBlogCategory', 'App\Http\Controllers\Admin@insertBlogCategory');
Route::post('/insertBlog', 'App\Http\Controllers\Admin@insertBlog');
// Route::post('/userupdate', 'App\Http\Controllers\Admin@userupdate');
// Route::post('/userupdate', 'App\Http\Controllers\Admin@userupdate');
// Route::post('/userupdate', 'App\Http\Controllers\Admin@userupdate');
// Route::post('/userupdate', 'App\Http\Controllers\Admin@userupdate');

