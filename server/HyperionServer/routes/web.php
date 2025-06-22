<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;

use App\Http\Controllers\CartController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| These routes are accessible to anyone and do not require a login token.
| They are perfect for registration, login, and public-facing content.
|
*/
Route::get('/', function () {
    $path = public_path('index.html');
    if (!File::exists($path)) {
        return response()->json(['message' => 'Fichier index.html non trouvé.'], 404);
    }
    return response()->file($path);
});

// Authentication and Registration
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/user', [UserController::class, 'store'])->name('user.store');

// Publicly viewable events
Route::get('/event', [EventController::class, 'index'])->name('event.index');
Route::get('/event/{id}', [EventController::class, 'show'])->name('event.show');


/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
|
| All routes within this group are protected by the 'auth:api' middleware.
| A valid JWT token must be provided in the Authorization header to access them.
|
*/
Route::middleware('auth:api')->group(function () {

    // Authenticated User actions
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    // User resource routes that require login
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/{id}/cards', [UserController::class, 'cards'])->name('user.cards');
    Route::get('/user/{id}/bookings', [UserController::class, 'bookings'])->name('user.bookings');
    Route::get('/user/{userId}/event/{eventId}/booking', [UserController::class, 'eventBooking'])->name('user.event.booking');
    Route::get('/user/{userId}/card/{cardId}/booking', [UserController::class, 'cardBooking'])->name('user.card.booking');
    Route::get('/user/{id}/events', [UserController::class, 'events'])->name('user.events');
    Route::post('/user/{id}/image', [UserController::class, 'updateImage'])->name('user.image.update');

    // Card resource routes that require login
    Route::get('/card', [CardController::class, 'index'])->name('card.index');
    Route::post('/card', [CardController::class, 'store'])->name('card.store');
    Route::get('/card/{id}', [CardController::class, 'show'])->name('card.show');
    Route::delete('/card/{id}', [CardController::class, 'destroy'])->name('card.destroy');
    Route::get('/card/search', [CardController::class, 'search'])->name('card.search');
    Route::get('/card/{id}/bookings', [CardController::class, 'bookings'])->name('card.bookings');

    // Event management routes that require login
    Route::post('/event', [EventController::class, 'store'])->name('event.store');
    Route::post('/event/{id}/book', [EventController::class, 'book'])->name('event.book');
    Route::post('/event/{id}/cancel', [EventController::class, 'cancel'])->name('event.cancel');
    Route::post('/event/{id}/image', [EventController::class, 'addImage'])->name('event.addImage');
    Route::delete('/event/{id}/image/{imageId}', [EventController::class, 'deleteImage'])->name('event.deleteImage');
    Route::get('/event/{id}/images', [EventController::class, 'getImages'])->name('event.getImages');
    Route::get('/event/{id}/bookings', [EventController::class, 'bookings'])->name('event.bookings');
    Route::put('/event/{id}', [EventController::class, 'update'])->name('event.update');


    // Cart management routes
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/events/{event}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/events/{event}', [CartController::class, 'remove'])->name('cart.remove');

    //Checkout Mangagement routes
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');


    //Booking Mangagement routes
    Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');

});
