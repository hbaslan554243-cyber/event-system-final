<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::latest()->paginate(20);
        return view('admin.resources.index', compact('resources'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'category'           => 'required|string',
            'quantity_total'     => 'required|integer|min:1',
            'quantity_available' => 'required|integer|min:0',
        ]);
        Resource::create($validated);
        return back()->with('success', 'Resource added.');
    }
    public function update(Request $request, Resource $resource)
    {
        $resource->update($request->only('name','category','quantity_total','quantity_available','unit','cost_per_unit','is_active'));
        return back()->with('success', 'Resource updated.');
    }
}
