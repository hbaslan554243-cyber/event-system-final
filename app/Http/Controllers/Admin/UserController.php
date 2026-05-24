<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('status')) $query->where('is_active', $request->status === 'active');
        if ($request->filled('search')) $query->where(fn($q) => $q->where('name','like','%'.$request->search.'%')->orWhere('email','like','%'.$request->search.'%'));
        $users = $query->withCount(['registrations','organizedEvents'])->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }
    public function show(User $user)
    {
        $user->load(['registrations.event','organizedEvents','payments']);
        $stats = [
            'total_registrations' => $user->registrations()->count(),
            'total_spent'         => $user->payments()->where('status','completed')->sum('amount'),
            'events_organized'    => $user->organizedEvents()->count(),
        ];
        return view('admin.users.show', compact('user','stats'));
    }
    public function create() { return view('admin.users.create'); }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role'     => 'required|in:admin,organizer,attendee',
            'phone'    => 'nullable|string|max:20',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }
    public function edit(User $user) { return view('admin.users.edit', compact('user')); }
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'role'      => 'required|in:admin,organizer,attendee',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);
        if ($request->filled('password')) $validated['password'] = Hash::make($request->password);
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }
}
