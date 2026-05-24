<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Event, Coupon};
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Event $event)
    {
        $coupons = Coupon::where('event_id', $event->id)->latest()->get();
        return view('organizer.coupons.index', compact('event','coupons'));
    }
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'code'        => 'required|string|unique:coupons',
            'type'        => 'required|in:percentage,fixed',
            'value'       => 'required|numeric|min:0',
            'max_uses'    => 'nullable|integer|min:1',
            'min_purchase'=> 'nullable|numeric|min:0',
            'valid_from'  => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
        ]);
        $validated['event_id'] = $event->id;
        Coupon::create($validated);
        return back()->with('success', 'Coupon created.');
    }
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
