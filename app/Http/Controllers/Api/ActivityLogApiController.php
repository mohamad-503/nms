<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogApiController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');
        if ($q = $request->get('search')) $query->where('action','like',"%{$q}%")->orWhere('module','like',"%{$q}%");
        return response()->json($query->latest()->paginate(50));
    }
}
