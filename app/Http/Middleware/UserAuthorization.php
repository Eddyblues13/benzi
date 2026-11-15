<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAuthorization
{
    /**
     * Require a logged-in user. JSON callers get a 401 instead of a redirect.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Your session has expired. Please log in again.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
