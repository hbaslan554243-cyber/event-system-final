<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('events')->get();
        return view('admin.categories.index', compact('categories'));
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:categories', 'icon' => 'nullable|string', 'color' => 'nullable|string|max:7']);
        Category::create($request->only('name','icon','color','description'));
        return back()->with('success', 'Category created.');
    }
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|unique:categories,name,'.$category->id]);
        $category->update($request->only('name','icon','color','description'));
        return back()->with('success', 'Category updated.');
    }
    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
