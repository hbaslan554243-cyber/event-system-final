<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Event, Category};
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('organizer','category','venue');
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('search'))   $query->where('title','like','%'.$request->search.'%');
        $events     = $query->withCount('registrations')->latest()->paginate(20)->withQueryString();
        $categories = Category::all();
        return view('admin.events.index', compact('events','categories'));
    }
    public function show(Event $event)
    {
        $event->load('organizer','category','venue','ticketTypes','registrations.user','feedbacks.user','media');
        $stats = [
            'total_registrations' => $event->registrations()->whereNotIn('status',['cancelled'])->count(),
            'revenue' => $event->payments()->where('payments.status','completed')->sum('amount'),
            'avg_rating'          => $event->feedbacks()->avg('overall_rating'),
            'checked_in'          => $event->registrations()->whereNotNull('checked_in_at')->count(),
        ];
        return view('admin.events.show', compact('event','stats'));
    }
    public function approve(Event $event)
{
    abort_if(!in_array($event->status, ['draft', 'pending_review']), 403, 'Only pending events can be approved.');
    $event->update(['status' => now() < $event->start_date ? 'upcoming' : 'ongoing']);
    return back()->with('success', 'Event approved and published.');
}
    public function cancel(Event $event)
    {
        $event->update(['status' => 'cancelled']);
        return back()->with('success', 'Event cancelled.');
    }
    public function feature(Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);
        return back()->with('success', 'Event featured status updated.');
    }
}
