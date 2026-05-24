<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Event, Announcement};
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'type'       => 'required|in:info,warning,urgent,update',
            'send_email' => 'boolean',
        ]);
        $validated['event_id']   = $event->id;
        $validated['created_by'] = Auth::id();
        $validated['sent_at']    = now();
        $announcement = Announcement::create($validated);
        if ($request->boolean('send_email')) {
            app(NotificationService::class)->sendEventAnnouncement($event, $announcement);
        }
        return back()->with('success', 'Announcement sent to all attendees.');
    }
}
