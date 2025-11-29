<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TeamInvitationController;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Filament\Notifications\Notification;

class FacebookController extends Controller
{
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebook()
    {
        // Log the redirect URI for debugging
        \Illuminate\Support\Facades\Log::info('Facebook OAuth redirect initiated', [
            'redirect_uri' => config('services.facebook.redirect'),
            'app_url' => config('app.url')
        ]);

        return Socialite::driver('facebook')
            ->scopes(['email'])
            ->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleFacebookCallback()
    {
        try {
            // Clear any session states that might cause redirect loops
            session()->forget('url.intended');

            // Get the OAuth response from the state cookie for enhanced debugging
            $state = request()->input('state');
            $code = request()->input('code');

            // Log the authorization data for debugging
            \Illuminate\Support\Facades\Log::info('Facebook OAuth attempt', [
                'state' => $state,
                'code_present' => !empty($code),
                'request_uri' => request()->getRequestUri(),
                'redirect_uri' => config('services.facebook.redirect')
            ]);

            $facebookUser = Socialite::driver('facebook')
                ->stateless()
                ->user();

            if (empty($facebookUser->email)) {
                throw new \Exception("Facebook did not provide an email address");
            }

            $user = User::where('email', $facebookUser->email)->first();

            if (!$user) {
                // Create a new user
                $user = User::create([
                    'name' => $facebookUser->name,
                    'email' => $facebookUser->email,
                    'facebook_id' => $facebookUser->id,
                    'password' => Hash::make(Str::random(10)), // Random password
                ]);

                // Assign the default student role
                $user->assignRole('student');

                // Display success notification
                Notification::make()
                    ->success()
                    ->title('Registration Successful')
                    ->body('Your account has been created successfully.')
                    ->send();
            } else {
                // Update facebook_id if not already set
                if (empty($user->facebook_id)) {
                    $user->update(['facebook_id' => $facebookUser->id]);
                }
            }

            // Login the user
            Auth::login($user);

            // Regenerate session to prevent session fixation
            session()->regenerate();

            // Prevent redirect loops by explicitly clearing these values
            session()->forget('url.intended');
            session()->forget('pending_invitation');

            // Check if the user has the proper role before redirecting
            if ($user->hasRole('admin') || $user->hasRole('instructor') || $user->hasRole('student')) {
                // User has proper role access, redirect to admin dashboard
                return redirect('/admin');
            } else {
                // User doesn't have any recognized role, redirect to login with message
                Auth::logout();
                return redirect()->route('student.login')
                    ->with('error', 'Your account does not have proper access. Please contact an administrator.');
            }

        } catch (\Exception $e) {
            // Enhanced error logging
            \Illuminate\Support\Facades\Log::error('Facebook authentication error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'state' => request()->input('state'),
                'code_present' => !empty(request()->input('code')),
                'redirect_uri' => config('services.facebook.redirect'),
                'app_url' => config('app.url'),
                'request_uri' => request()->getRequestUri()
            ]);

            // Determine a more specific error message based on the exception
            $errorMessage = 'There was an error authenticating with Facebook. Please try again later.';

            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                $errorMessage = 'Your account already exists in our system. Please try logging in directly.';
            } elseif (strpos($e->getMessage(), 'invalid_grant') !== false) {
                $errorMessage = 'The authentication request expired or was revoked. Please try again.';
            } elseif (strpos($e->getMessage(), 'redirect_uri_mismatch') !== false) {
                $errorMessage = 'There was a configuration error. Please contact support.';
            }

            // Display error notification
            Notification::make()
                ->danger()
                ->title('Facebook Authentication Failed')
                ->body($errorMessage)
                ->send();

            return redirect()->route('student.login')
                ->with('error', $errorMessage);
        }
    }
}
