<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AssignUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && !$user->roles->count()) {
            switch ($user->user_type) {
                case 'admin':
                    $user->assignRole('admin');
                    break;
                case 'delivery_man':
                    $user->assignRole('delivery_man');
                    break;
                case 'client':
                    $user->assignRole('client');
                    break;
                // Add more cases as needed
            }
        }

        return $next($request);
    }
}
