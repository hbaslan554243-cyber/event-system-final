<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{User, Event, Registration, Payment, Feedback, Category};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'         => User::count(),
            'total_events'        => Event::count(),
            'total_registrations' => Registration::whereNotIn('status', ['cancelled'])->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_feedbacks'   => Feedback::where('is_approved', false)->count(),
            'active_events'       => Event::whereIn('status', ['upcoming', 'ongoing'])->count(),
        ];
        $recent_events        = Event::with('organizer','category')->latest()->take(5)->get();
        $recent_registrations = Registration::with('user','event')->latest()->take(10)->get();
        $monthly_revenue      = Payment::where('status', 'completed')
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->whereYear('paid_at', now()->year)
            ->groupBy('month')->pluck('total', 'month');
        $category_stats = Category::withCount('events')->get();
        $top_events     = Event::withCount(['registrations' => fn($q) => $q->where('status','!=','cancelled')])
            ->orderByDesc('registrations_count')->take(5)->get();
        return view('admin.dashboard', compact(
            'stats','recent_events','recent_registrations','monthly_revenue','category_stats','top_events'
        ));
    }
}
