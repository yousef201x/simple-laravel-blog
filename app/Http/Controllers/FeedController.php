<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function handleFeedCategory(Request $request)
    {
        // Check if the 'category' parameter is present in the request
        if ($request->category) {
            // Retrieve the category name from the request
            $categoryName = $request->category;

            // Fetch posts that belong to the category with the specified name
            $posts = Post::whereHas('category', function ($query) use ($categoryName) {
                // Filter the categories by the given name
                $query->where('name', $categoryName);
            })->paginate(25); // Paginate the results to display 25 posts per page
        } else {
            // If no category is specified, fetch all posts
            $posts = Post::select('id', 'title', 'content', 'user_id', 'category_id', 'created_at')
                ->paginate(25); // Paginate the results to display 25 posts per page
        }

        // Return the 'feed.index' view, passing the retrieved posts to the view
        return view('feed.index', compact('posts'));
    }
}
