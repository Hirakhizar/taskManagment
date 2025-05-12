<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function index()
    {
         $user=Auth::user();
        if (in_array($user->role, ['admin','manager'])) {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($request->only('name'));

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }
     public function delete($id)
    {
       $category=Category::find($id);
       if( !$category){
            return redirect()->route('users.index')->with('error', 'category not found.');
       }

       $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' .$id,
        ]);
       $category= Category::find($id);
        $category->update($request->only('name'));

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

}
