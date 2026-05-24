<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::withCount('events')->latest()->paginate(20);
        return view('admin.venues.index', compact('venues'));
    }
    public function create() { return view('admin.venues.create'); }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'address'  => 'required|string',
            'city'     => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
        ]);
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $img) $images[] = $img->store('venues', 'public');
            $validated['images'] = $images;
        }
        $validated['amenities'] = $request->input('amenities', []);
        Venue::create($validated);
        return redirect()->route('admin.venues.index')->with('success', 'Venue created.');
    }
    public function edit(Venue $venue) { return view('admin.venues.edit', compact('venue')); }
    public function update(Request $request, Venue $venue)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'address'  => 'required|string',
            'city'     => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
        ]);
        $validated['amenities'] = $request->input('amenities', []);
        $venue->update($validated);
        return redirect()->route('admin.venues.index')->with('success', 'Venue updated.');
    }
    public function destroy(Venue $venue)
    {
        $venue->delete();
        return redirect()->route('admin.venues.index')->with('success', 'Venue deleted.');
    }
}
