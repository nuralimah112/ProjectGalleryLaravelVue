<?php

// app/Http/Controllers/CommentController.php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Photo $photo)
    {
        $comments = $photo->comments()->with('user')->latest()->get();
        return response()->json($comments);
    }
    
    

// Controller method for adding a comment
public function store(Request $request, $photoId)
{
    // Validate the input
    $validated = $request->validate([
        'content' => 'required|string',
    ]);

    // Create a new comment
    $comment = new Comment();
    $comment->content = $validated['content'];
    $comment->user_id = auth()->id();  // Assuming the user is logged in
    $comment->photo_id = $photoId;
    $comment->save();

    // Include user data in the response
    $comment->load('user'); // Eager load the 'user' relationship (assuming you have it defined)

    return response()->json($comment);
}


public function destroy(Comment $comment)
{
    // Ensure the logged-in user is the owner of the comment
    if ($comment->user_id === Auth::id()) {
        $comment->delete();
        return response()->json(null, 204);
    }

    return response()->json(['message' => 'Unauthorized'], 403);
}

}
