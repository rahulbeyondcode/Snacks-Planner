<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;

class RedirectIfAuthenticated extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $guard
     * @return string|null
     */
    public function redirectTo(Request $request, $guard = null)
    {
        return '/home';
    }
}
