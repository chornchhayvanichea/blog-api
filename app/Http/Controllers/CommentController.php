<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = Comment::where('post_id', $post->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse([
            'comments' => CommentResource::collection($comments),
        ], 'Comments retrieved successfully');
    }

    public function store(CommentRequest $request, Post $post)
    {
        $validated = $request->validated();
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return $this->sendResponse([
            'comment' => new CommentResource($comment->load('user'))
        ], 'comment created successfully');
    }

    public function update(Post $post, Comment $comment, CommentRequest $request)
    {
        $this->authorize('update', $comment);
        $validated = $request->validated();
        $comment->update($validated);

        return $this->sendResponse([
            'comment' => new CommentResource($comment->load('user'))
        ], 'comment updated successfully');
    }

    public function destroy(Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return $this->sendResponse([], 'comment deleted');
    }
}
