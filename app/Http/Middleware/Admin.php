<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (!is_null($user)) {
            auth()->login($user);

            if ($user->is_admin) {
                return $next($request);
            } else {
                return  response()->json(['message' => 'Forbidden'], 403);
            }
        }
        return  response()->json(['message' => 'Unauthorized'], 401);
        // return  ResponderFacade::unauthorized();
    }
}
