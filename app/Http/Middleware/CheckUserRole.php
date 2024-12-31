<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // السماح بالوصول إلى صفحة تسجيل الدخول وطلبات Livewire
        if ($request->is('login') || $request->is('livewire/*') || $request->is('_debugbar/*')) {
            return $next($request);
        }

        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
