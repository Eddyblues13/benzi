<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActivated
{
    /**
     * Send users who have not confirmed their email code to the verify page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && ! $user->isActivated()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please verify your account first.'], 403);
            }

            return redirect()->route('verify');
        }

        return $next($request);
    }
}
