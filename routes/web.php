<?php

use App\Http\Controllers\Stripe\RefreshDriverStripeOnboardingController;
use App\Http\Controllers\Stripe\ReturnDriverStripeOnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/login', fn () => view('auth.login'))
    ->name('login');
Route::get('/register', fn () => view('auth.register'))
    ->name('register');
Route::get('/home', fn () => view('home'))
    ->name('home');
Route::get('/profile', fn () => view('profile'))
    ->name('profile');
Route::get('/stripe/connect/refresh/{driverProfile}', RefreshDriverStripeOnboardingController::class)
    ->middleware('signed')
    ->name('stripe.connect.refresh');
Route::get('/stripe/connect/return/{driverProfile}', ReturnDriverStripeOnboardingController::class)
    ->middleware('signed')
    ->name('stripe.connect.return');
