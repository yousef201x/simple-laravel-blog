<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Fetch posts with their categories and users, optionally filter by search query
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $posts = Post::where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('content', 'like', '%' . $searchTerm . '%')
                ->with(['category', 'user'])
                ->paginate(25);
        } else {
            $posts = Post::with(['category', 'user'])->paginate(25);
        }

        // Return a view named 'index' and pass the $posts variable to the view
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        return view('posts.create', compact(['categories', 'users']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_path' => 'nullable|image',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {

            if ($request->file('image_path')) {
                $imagePath = $request->file('image_path')->store('images');
                $imagePath = asset('storage/' . $imagePath);
                Post::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'image_path' => $imagePath,
                    'category_id' => $request->category_id,
                    'user_id' => $request->user_id,
                ]);
            } else {
                // Create a new post using the validated data
                Post::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'category_id' => $request->category_id,
                    'user_id' => $request->user_id,
                ]);
            }

            // Return a redirect with a success flash session message
            return redirect()->route('posts.index')->with('success', 'Post created successfully');
        } catch (\Exception $e) {
            // Handle the exception, maybe log it or display an error message
            return redirect()->back()->withInput()->withErrors(['error' => 'Error creating post']);
        }
    }

    public function edit($id)
    {
        // Find the post by id
        $post = Post::find($id);
        $categories = Category::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        return view('posts.edit', compact(['post', 'categories', 'users']));

        if ($post) {
            return view('posts.edit', compact('post', 'categories'));
        } else {
            return redirect()->route('posts.index')->withErrors(['error' => 'Post not found']);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Find post
            $post = Post::find($id);

            if ($post) {
                // Update the post with the validated data
                $post->update($validatedData);

                // Redirect back to the index with a success flash session message
                return redirect()->route('posts.index')->with('success', 'Post updated successfully');
            } else {
                return redirect()->route('posts.index')->withErrors(['error' => 'Post not found']);
            }
        } catch (\Exception $e) {
            // Handle the exception, maybe log it or display an error message
            return redirect()->back()->withInput()->withErrors(['error' => 'Error updating post']);
        }
    }

    public function destroy($id)
    {
        try {
            // Find post
            $post = Post::find($id);

            if ($post) {
                // Delete the post
                $post->delete();
                // Redirect back to the index with a success flash session message
                return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
            } else {
                return redirect()->route('posts.index')->withErrors(['error' => 'Post not found']);
            }
        } catch (\Exception $e) {
            // Handle the exception, maybe log it or display an error message
            return redirect()->back()->withErrors(['error' => 'Error deleting post']);
        }
    }
}
