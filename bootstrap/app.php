<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude logout from CSRF token verification to prevent 419 Page Expired errors on mobile/stale sessions
        $middleware->validateCsrfTokens(except: [
            'logout',
            '/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Gracefully handle token expiration (419) without crashing to a blank error screen
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->is('logout') || $request->routeIs('logout')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('info', 'You have been logged out successfully.');
            }

            return redirect()->back()->with('error', 'Your session expired due to inactivity. Please refresh and try again.');
        });
    })->create();
