<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request with more graceful error handling.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip routes that should bypass this middleware to prevent redirect loops
        if ($request->is('auth/google*') || $request->is('login*') || $request->is('register*') ||
            $request->is('forgot-password*') || $request->is('reset-password*')) {
            return $next($request);
        }

        // First ensure we have an authenticated user
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // Check if user is admin - wrapped in try/catch for safety
            $isAdmin = method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;

            // Check if user is project advisor - new role
            $isProjectAdvisor = method_exists($user, 'hasRole') ? $user->hasRole('project_advisor') : false;

            // Check if user has teams - wrapped in try/catch for safety
            $hasTeams = method_exists($user, 'teams') ? ($user->teams()->count() > 0) : false;

            // Allow if any condition is met
            if ($isAdmin || $hasTeams || $isProjectAdvisor) {
                // Set default redirect but don't cause issues if this fails
                try {
                    config(['filament.default_redirect_url' => '/admin']);
                } catch (\Exception $e) {
                    // Silently continue if config setting fails
                }

                return $next($request);
            }

            // User is authenticated but lacks permissions - redirect to login instead of dashboard
            // to avoid potential loops, with a clear error message
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'You need team membership or admin privileges to access the system. Please contact an administrator if you need access.');

        } catch (\Exception $e) {
            // Fallback if any check throws an exception
            // Allow access but log the error
            report($e);
            return $next($request);
        }
    }
}
