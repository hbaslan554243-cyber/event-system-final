<?php
namespace App\Http\Controllers;

use App\Models\{Event, Registration, Feedback};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create(Event $event)
    {
        // Allow any confirmed/attended registrant to review
        $registration = Registration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['confirmed', 'attended', 'pending'])
            ->first();

        abort_if(!$registration, 403, 'You must be registered for this event to leave a review.');

        $alreadyReviewed = Feedback::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        abort_if($alreadyReviewed, 422, 'You already submitted a review for this event.');

        return view('attendee.feedback.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'overall_rating'      => 'required|integer|between:1,5',
            'venue_rating'        => 'nullable|integer|between:1,5',
            'organization_rating' => 'nullable|integer|between:1,5',
            'content_rating'      => 'nullable|integer|between:1,5',
            'comment'             => 'nullable|string|max:2000',
            'suggestions'         => 'nullable|string|max:1000',
            'would_recommend'     => 'boolean',
        ]);

        $validated['event_id']        = $event->id;
        $validated['user_id']         = Auth::id();
        $validated['would_recommend'] = $request->boolean('would_recommend');
        $validated['is_approved']     = true;
        $validated['is_public']       = true;

        Feedback::create($validated);

        // Update event average rating
        $avg   = Feedback::where('event_id', $event->id)->avg('overall_rating');
        $count = Feedback::where('event_id', $event->id)->count();
        $event->update(['avg_rating' => round($avg, 2), 'total_reviews' => $count]);

        return redirect()->route('attendee.dashboard')
                         ->with('success', '⭐ Thank you for your review!');
    }
}