<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');
        if ($q = $request->get('search')) {
            $query->where('action', 'like', "%{$q}%")
                ->orWhere('module', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
        }
        return Inertia::render('Logs/Index', ['logs' => $query->latest()->paginate(50)->withQueryString()]);
    }
}
