<?php
namespace App\Http\Controllers;
use App\Models\{Event, Registration, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventBrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::published()->with('category','venue','organizer');
        if ($request->filled('category'))  $query->where('category_id', $request->category);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('search'))    $query->where('title','like','%'.$request->search.'%');
        if ($request->filled('date'))      $query->whereDate('start_date', $request->date);
        if ($request->boolean('free'))     $query->whereHas('ticketTypes', fn($q) => $q->where('type','free'));
        if ($request->boolean('online'))   $query->where('is_online', true);
        $events     = $query->orderBy('start_date')->paginate(12)->withQueryString();
        $categories = Category::withCount('events')->get();
        $featured   = Event::published()->featured()->with('category','venue')->take(3)->get();
        return view('events.index', compact('events','categories','featured'));
    }
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->with('category','venue','organizer','ticketTypes','media','feedbacks.user')->firstOrFail();
        $userRegistration = Auth::check()
            ? Registration::where(['event_id' => $event->id, 'user_id' => Auth::id()])->whereNotIn('status',['cancelled'])->first()
            : null;
        $relatedEvents = Event::published()->where('category_id', $event->category_id)->where('id','!=',$event->id)->take(4)->get();
        $reviews       = $event->feedbacks()->where('is_approved',true)->where('is_public',true)->latest()->take(10)->get();
        return view('events.show', compact('event','userRegistration','relatedEvents','reviews'));
    }
}
