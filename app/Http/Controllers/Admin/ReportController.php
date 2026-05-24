<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Payment, Registration, Event, Feedback};
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index() { return view('admin.reports.index'); }

    public function revenue(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month');
        $query = Payment::where('status','completed')->whereYear('paid_at', $year);
        if ($month) $query->whereMonth('paid_at', $month);
        $data = [
            'total_revenue' => $query->sum('amount'),
            'by_gateway'    => Payment::where('status','completed')->whereYear('paid_at',$year)->groupBy('gateway')->selectRaw('gateway, SUM(amount) as total, COUNT(*) as count')->get(),
            'monthly'       => Payment::where('status','completed')->whereYear('paid_at',$year)->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')->groupBy('month')->pluck('total','month'),
            'by_event'      => collect(),
        ];
        return view('admin.reports.revenue', compact('data','year','month'));
    }

    public function attendees()
    {
        $data = [
            'total'      => Registration::whereNotIn('status',['cancelled'])->count(),
            'by_status'  => Registration::groupBy('status')->selectRaw('status, COUNT(*) as count')->pluck('count','status'),
            'top_events' => Event::withCount(['registrations' => fn($q) => $q->where('status','!=','cancelled')])->orderByDesc('registrations_count')->take(10)->get(),
            'by_month'   => Registration::selectRaw('MONTH(created_at) as month, COUNT(*) as count')->whereYear('created_at', now()->year)->groupBy('month')->pluck('count','month'),
        ];
        return view('admin.reports.attendees', compact('data'));
    }

    public function feedback()
    {
        $data = [
            'avg_rating'    => Feedback::avg('overall_rating'),
            'total_reviews' => Feedback::count(),
            'by_rating'     => Feedback::groupBy('overall_rating')->selectRaw('overall_rating, COUNT(*) as count')->orderBy('overall_rating','desc')->pluck('count','overall_rating'),
            'recent'        => Feedback::with('event','user')->latest()->take(20)->get(),
            'top_rated'     => Event::where('total_reviews','>',0)->orderByDesc('avg_rating')->take(10)->get(),
        ];
        return view('admin.reports.feedback', compact('data'));
    }

    public function exportCsv(Request $request, string $type)
    {
        $filename = $type . '_report_' . now()->format('Y-m-d') . '.csv';
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];
        $data     = match($type) {
            'registrations' => Registration::with('user','event','ticketType')->get(),
            'payments'      => Payment::with('user','registration.event')->get(),
            'events'        => Event::with('organizer','category')->get(),
            default         => collect(),
        };
        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            if ($type === 'payments') {
                fputcsv($file, ['Transaction ID','User','Event','Amount','Gateway','Status','Date']);
                foreach ($data as $row) fputcsv($file, [$row->transaction_id, $row->user->name, $row->registration?->event?->title, $row->amount, $row->gateway, $row->status, $row->paid_at]);
            } elseif ($type === 'registrations') {
                fputcsv($file, ['Reg #','User','Email','Event','Ticket','Qty','Amount','Status','Date']);
                foreach ($data as $row) fputcsv($file, [$row->registration_number, $row->user->name, $row->user->email, $row->event->title, $row->ticketType->name, $row->quantity, $row->final_amount, $row->status, $row->created_at]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
