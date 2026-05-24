<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Registration, Payment};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $organizer = Auth::user();
        $eventIds  = $organizer->organizedEvents()->pluck('id');
        $stats = [
            'total_events'        => $organizer->organizedEvents()->count(),
            'active_events'       => $organizer->organizedEvents()->whereIn('status',['upcoming','ongoing'])->count(),
            'total_registrations' => Registration::whereIn('event_id', $eventIds)->whereNotIn('status',['cancelled'])->count(),
            'total_revenue' => Payment::whereIn('registration_id',
    Registration::whereIn('event_id', $eventIds)->pluck('id')
)->where('status', 'completed')->sum('amount'),
        ];
        $upcoming_events = $organizer->organizedEvents()->where('status','upcoming')->orderBy('start_date')->take(5)->get();
        $recent_registrations = Registration::with('user','event','ticketType')
            ->whereIn('event_id', $eventIds)->latest()->take(10)->get();
        return view('organizer.dashboard', compact('stats','upcoming_events','recent_registrations'));
    }
}
