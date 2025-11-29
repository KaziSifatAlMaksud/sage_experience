<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    // Main consolidated login route for all user types
    Route::view('login', 'auth.login')
        ->name('login');

    // Add redirects for the old login routes
    Route::redirect('login/student', '/login')
        ->name('student.login');

    Route::redirect('login/instructor', 'login')
        ->name('instructor.login');

    Route::redirect('login/admin', 'login')
        ->name('admin.login');

    // Add POST route for login form submission
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.post');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');

    // Google Login Routes
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
        ->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('google.callback');

    // Facebook Login Routes
    Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])
        ->name('facebook.login');
    Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback'])
        ->name('facebook.callback');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
