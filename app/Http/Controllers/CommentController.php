<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = Comment::where('post_id', $post->id)->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'comments' => $comments
        ]);
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

    public function update(Post $post, Comment $comment, CommentRequest $request)
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

    public function destroy(Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json([
              'message' => 'Comment has been deleted'
          ]);
    }

}
