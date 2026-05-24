<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Event, Category, Venue};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Auth::user()->organizedEvents()->with('category','venue')->withCount('registrations')->latest()->paginate(15);
        return view('organizer.events.index', compact('events'));
    }
    public function create()
    {
        $categories = Category::all();
        $venues     = Venue::where('is_active', true)->get();
        return view('organizer.events.create', compact('categories','venues'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'venue_id'          => 'nullable|exists:venues,id',
            'description'       => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date'        => 'required|date|after:now',
            'end_date'          => 'required|date|after:start_date',
            'max_attendees'     => 'nullable|integer|min:1',
            'is_online'         => 'boolean',
            'online_meeting_url'=> 'nullable|url',
            'banner_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        if ($validated['venue_id'] ?? false) {
            $venue = Venue::find($validated['venue_id']);
            if ($venue->hasConflict(new \DateTime($validated['start_date']), new \DateTime($validated['end_date']))) {
                return back()->withErrors(['venue_id' => 'This venue is already booked for the selected dates.'])->withInput();
            }
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }
        $validated['organizer_id'] = Auth::id();
        $validated['slug']         = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status']       = 'draft';
        $validated['is_online']    = $request->boolean('is_online');
        $event = Event::create($validated);
        return redirect()->route('organizer.events.show', $event)->with('success', 'Event created! Add ticket types to complete setup.');
    }
    public function show(Event $event)
    {
        $this->authorizeEvent($event);
        $event->load('ticketTypes','registrations.user','media','feedbacks','announcements','resources');
        $stats = [
            'registrations' => $event->registrations()->whereNotIn('status',['cancelled'])->count(),
            'revenue' => $event->payments()->where('payments.status','completed')->sum('amount'),
            'checked_in'    => $event->registrations()->whereNotNull('checked_in_at')->count(),
            'avg_rating'    => $event->feedbacks()->avg('overall_rating'),
        ];
        return view('organizer.events.show', compact('event','stats'));
    }
    public function edit(Event $event)
    {
        $this->authorizeEvent($event);
        $categories = Category::all();
        $venues     = Venue::where('is_active', true)->get();
        return view('organizer.events.create', compact('event','categories','venues'));
    }
    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);
        abort_if(in_array($event->status, ['completed','cancelled']), 403, 'Cannot edit this event.');
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'venue_id'          => 'nullable|exists:venues,id',
            'description'       => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after:start_date',
            'max_attendees'     => 'nullable|integer|min:1',
            'banner_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        if ($request->hasFile('banner_image')) {
            if ($event->banner_image) Storage::disk('public')->delete($event->banner_image);
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }
        $event->update($validated);
        return redirect()->route('organizer.events.show', $event)->with('success', 'Event updated successfully.');
    }
    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        abort_if($event->registrations()->whereNotIn('status',['cancelled'])->exists(), 403, 'Cannot delete event with active registrations.');
        $event->delete();
        return redirect()->route('organizer.events.index')->with('success', 'Event deleted.');
    }
    public function submitForReview(Event $event)
{
    $this->authorizeEvent($event);
    abort_if($event->status !== 'draft', 403, 'Only draft events can be submitted for review.');
    abort_if(!$event->ticketTypes()->where('is_active',true)->exists(), 422, 'Add at least one ticket type before submitting.');
    $event->update(['status' => 'pending_review']);
    return back()->with('success', 'Event submitted for admin review. You will be notified once approved.');
}
    private function authorizeEvent(Event $event): void
    {
        abort_if($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin(), 403);
    }
}
