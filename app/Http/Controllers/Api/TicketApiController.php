<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['customer','assignee']);
        if ($q = $request->get('search')) $query->where('subject','like',"%{$q}%");
        return response()->json($query->latest()->paginate(15));
    }
    public function store(Request $request) { return response()->json(SupportTicket::create($request->validate(['customer_id'=>'nullable|exists:customers,id','subject'=>'required|string','description'=>'nullable|string','priority'=>'in:low,medium,high,urgent','assigned_to'=>'nullable|exists:users,id'])), 201); }
    public function show(SupportTicket $ticket) { return response()->json($ticket->load(['customer','assignee','replies.author'])); }
    public function update(Request $request, SupportTicket $ticket) { $ticket->update($request->validate(['status'=>'in:open,assigned,in_progress,resolved,closed','priority'=>'in:low,medium,high,urgent','assigned_to'=>'nullable|exists:users,id'])); return response()->json($ticket); }
    public function destroy(SupportTicket $ticket) { $ticket->delete(); return response()->json(null, 204); }
    public function reply(Request $request, SupportTicket $ticket) { $r = TicketReply::create(['ticket_id'=>$ticket->id,'author_id'=>auth()->id(),'message'=>$request->validate(['message'=>'required|string'])['message']]); if (in_array($ticket->status,['open','assigned'])) $ticket->update(['status'=>'in_progress']); return response()->json($r, 201); }
}
