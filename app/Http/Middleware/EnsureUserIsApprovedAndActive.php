<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApprovedAndActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Check if user is suspended
        if ($user->is_suspended) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Please contact an administrator.'
            ]);
        }

        // Check if user needs approval
        if (!$user->is_approved) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is pending approval. Please wait for an administrator to approve your account.'
            ]);
        }

        return $next($request);
    }
}
