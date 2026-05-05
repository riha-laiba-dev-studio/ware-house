<?php
namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginLog::with('user')->latest();

        if ($request->status)  $query->where('status', $request->status);
        if ($request->email)   $query->where('email', 'like', '%'.$request->email.'%');
        if ($request->from)    $query->whereDate('created_at', '>=', $request->from);
        if ($request->to)      $query->whereDate('created_at', '<=', $request->to);

        $logs = $query->paginate(50)->withQueryString();

        $stats = [
            'total'   => LoginLog::count(),
            'success' => LoginLog::where('status', 'success')->count(),
            'failed'  => LoginLog::where('status', 'failed')->count(),
            'today'   => LoginLog::whereDate('created_at', today())->count(),
        ];

        return view('users.login-logs', compact('logs', 'stats'));
    }
}
