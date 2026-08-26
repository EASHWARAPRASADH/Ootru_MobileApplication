<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use App\Models\Setting;

class InjectSettings
{
    public function handle($request, Closure $next)
    {
        $themeColor = AppSetting::all()->pluck('color')[0];
        view()->share('themeColor', $themeColor);

        return $next($request);
    }
}
