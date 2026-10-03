<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;

class AdminSession
{
    public function handle(Request $request, Closure $next)
    {
        $admin = AdminUser::find($request->session()->get('admin_id'));

        if (!$admin || !$admin->is_active) {
            $request->session()->forget('admin_id');
            return response()->json(['message' => 'Unauthorised'], 401);
        }

        return $next($request);
    }
}
