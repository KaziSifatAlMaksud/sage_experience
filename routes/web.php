<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware('auth')->post('/profile/delete', [AuthenticatedSessionController::class, 'destroy'])->name('profile.destroy');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
       Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            return redirect('/admin');
        } else {
            return redirect('/admin/user-insights');
        }
    })->name('dashboard');

    Route::view('profile', 'profile')->name('profile');
});

// Team Invitation Route - Accessible without authentication and at root level
Route::get('join-team/{token}', [\App\Http\Controllers\TeamInvitationController::class, 'accept'])
    ->name('team.invitation.accept');

// Also register the same route without the leading slash to catch potential URL variations
Route::get('join-team/{token}', [\App\Http\Controllers\TeamInvitationController::class, 'accept']);

require __DIR__.'/auth.php';
