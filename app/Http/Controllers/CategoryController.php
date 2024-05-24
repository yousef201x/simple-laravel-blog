<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function index()
    {
        // Fetch categories with only the specified columns: id, name, created_at
        $categories = Category::select('id', 'name', 'created_at')->get();

        // Return a view named 'index' and pass the $categories variable to the view
        return view('categories.index')->with(compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'string|max:100',
            ]);

            // Create a new category using the validated data
            Category::create($validatedData);

            // Return a redirect with a success flash session message
            return redirect()->route('categories.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            // Handle the exception, maybe log it or display an error message
            return redirect()->back()->withInput()->withErrors(['error' => 'Error creating category']);
        }
    }

    public function edit($id)
    {
        // Return the edit view with the category data
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'string|max:100',
            ]);

            // Find category
            $category = Category::find($id);

            // Update the category with the validated data
            $category->update($validatedData);

            // Redirect back to the index with a success flash session message
            return redirect()->route('categories.index')->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            // Handle the exception, maybe log it or display an error message
            return redirect()->back()->withInput()->withErrors(['error' => 'Error updating category']);
        }
    }

    public function destroy($id)
    {
        try {
            // find category
            $category = Category::find($id);
            if ($category) {
                // Delete the category
                $category->delete();
                // Redirect back to the index with a success flash session message
                return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
            } else {
                return redirect()->back()->withErrors(['error' => 'Category not found']);
            }
        } catch (\Exception $e) {
            // Handle the exception, maybe log it or display an error message
            return redirect()->back()->withErrors(['error' => 'Error deleting category']);
        }
    }
}
