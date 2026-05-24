<?php
namespace App\Http\Controllers;
use App\Models\{Registration, Payment};
use Illuminate\Support\Facades\Auth;

class AttendeeDashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $stats = [
            'upcoming_events'  => Registration::where('user_id', $user->id)->where('status','confirmed')
                                    ->whereHas('event', fn($q) => $q->where('start_date','>',now()))->count(),
            'attended_events'  => Registration::where(['user_id' => $user->id, 'status' => 'attended'])->count(),
            'total_spent'      => Payment::where(['user_id' => $user->id, 'status' => 'completed'])->sum('amount'),
            'pending_feedback' => Registration::where(['user_id' => $user->id, 'status' => 'attended'])
                                    ->whereDoesntHave('event.feedbacks', fn($q) => $q->where('user_id', $user->id))->count(),
        ];
        $upcoming = Registration::with('event.venue','ticketType')
            ->where('user_id', $user->id)->where('status','confirmed')
            ->whereHas('event', fn($q) => $q->where('start_date','>',now()))
            ->take(5)->get();
        return view('attendee.dashboard', compact('stats','upcoming'));
    }
}
