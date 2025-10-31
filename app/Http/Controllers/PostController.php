<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\CreatePostRequest;
use App\Http\Requests\PostRequests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {

        $query = Post::query()->with(['user','viewers']);

        if ($request->boolean('mine')) {
            // show all posts for current user (draft + published)
            $query->where('user_id', auth()->id());
        } else {
            // show only published posts
            $query->where('status', 'published');
        }

        $query->orderBy('created_at', 'desc');
        $posts = $query->paginate(10);

        return $this->sendResponse([
            'posts' => PostResource::collection($posts),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]
        ], 'Posts retrieved successfully');
    }

    public function show(Post $post)
    {
        $post->load(['user','likes','viewers']);
        return $this->sendResponse([
            'post' => new PostResource($post),
        ], 'Post retrieved successfully');
    }

    public function store(CreatePostRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            ...$validated,
            'user_id' => auth()->id(),
            'slug' => Str::slug($validated['title']) . '-' . now()->format('YmdHis'),
        ]);

        return $this->sendResponse([
            'post' => new PostResource($post)
        ], 'Post has been created successfully');
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validated();
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);
        $post->refresh();
        return $this->sendResponse([
            'post' => new PostResource($post)
        ], 'Post has been updated successfully');
    }

    // this is delete not destroy btw.
    public function delete(Post $post)
    {

        $this->authorize('delete', $post);
        $post->delete();
        $trashed = Post::onlyTrashed()->get();

        return $this->sendResponse([], 'Post has been deleted successfully');
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $post);
        $post->restore();

        return $this->sendResponse([
            'post' => new PostResource($post)
        ], 'Post restored successfully');
    }
    public function viewIncrement(Post $post)
    {
        if (auth()->check()) {
            $post->viewers()->syncWithoutDetaching([auth()->id()]);
        }

        return $this->sendResponse([
            'views' => $post->viewers()->count(),
        ], null);
    }
}
