<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Customer;
use App\Models\User;
use App\Models\TicketReply;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['customer', 'assignee']);
        if ($q = $request->get('search')) {
            $query->where('subject', 'like', "%{$q}%");
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        return Inertia::render('Tickets/Index', [
            'tickets' => $query->latest()->paginate(15)->withQueryString(),
            'customers' => Customer::all(['id', 'full_name']),
            'staff' => User::all(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'customers' => Customer::all(['id', 'full_name']),
            'staff' => User::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $ticket = SupportTicket::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'create', 'module' => 'tickets', 'description' => "تذكرة: {$ticket->subject}", 'ip_address' => request()->ip()]);
        return redirect()->route('tickets.index')->with('success', 'تم إنشاء التذكرة');
    }

    public function show(SupportTicket $ticket)
    {
        return Inertia::render('Tickets/Show', ['ticket' => $ticket->load(['customer', 'assignee', 'replies.author'])]);
    }

    public function edit(SupportTicket $ticket)
    {
        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket,
            'customers' => Customer::all(['id', 'full_name']),
            'staff' => User::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high,urgent',
            'status' => 'in:open,assigned,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $ticket->update($data);
        return redirect()->route('tickets.index')->with('success', 'تم تحديث التذكرة');
    }

    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'تم حذف التذكرة');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate(['message' => 'required|string']);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'author_id' => auth()->id(),
            'message' => $data['message'],
        ]);
        if (in_array($ticket->status, ['open', 'assigned'])) {
            $ticket->update(['status' => 'in_progress']);
        }
        return back()->with('success', 'تم إرسال الرد');
    }
}
