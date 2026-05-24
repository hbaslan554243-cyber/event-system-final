<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\{Event, TicketType};
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'type'               => 'required|in:free,paid,vip,donation',
            'price'              => 'nullable|numeric|min:0',
            'quantity_available' => 'required|integer|min:1',
            'max_per_person'     => 'nullable|integer|min:1|max:20',
            'description'        => 'nullable|string',
            'sale_start'         => 'nullable|date',
            'sale_end'           => 'nullable|date|after:sale_start',
        ]);
        $validated['event_id'] = $event->id;
        if ($validated['type'] === 'free') $validated['price'] = 0;
        TicketType::create($validated);
        return back()->with('success', 'Ticket type added.');
    }
    public function update(Request $request, TicketType $ticketType)
    {
        $ticketType->update($request->only('name','price','quantity_available','max_per_person','description','is_active'));
        return back()->with('success', 'Ticket type updated.');
    }
    public function destroy(TicketType $ticketType)
    {
        abort_if($ticketType->registrations()->exists(), 403, 'Cannot delete ticket type with registrations.');
        $ticketType->delete();
        return back()->with('success', 'Ticket type deleted.');
    }
}
