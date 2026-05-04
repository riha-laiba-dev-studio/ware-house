<?php
namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (auth()->check() && !$request->isMethod('GET')) {
            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => $request->method().' '.$request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        return $response;
    }
}
