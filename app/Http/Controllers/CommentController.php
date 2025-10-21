<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {

    }
    public function store(CommentRequest $request, Post $post)
    {
        $validated = $request->validated();
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content']
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
            'comment' => $comment->load('post')
        ], 200);
    }

    public function edit(CommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $validated = $request->validated();
        $comment->update($validated);
        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
            'comment' => $comment->load('post')
        ], 200);
    }

    public function delete(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json([
              'message' => 'Comment has been deleted'
          ]);



    }

}
