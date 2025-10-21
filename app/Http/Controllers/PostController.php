<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\PostRequests\CreatePostRequest;
use App\Http\Requests\PostRequests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()->with('user');

        // Default: only show published posts
        $query->where('status', 'published');

        // Optional: if user wants to see their own posts, include drafts
        if ($request->has('mine') && $request->query('mine') == 1) {
            $query->orWhere('user_id', auth()->id());
        }

        $query->orderBy('created_at', 'desc');

        $posts = $query->paginate(10);

        return response()->json([
            'posts' => $posts->items(),
            'total' => $posts->total(),
            'per_page' => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage()
        ]);
    }
    public function show(Post $post)
    {
        return response()->json([
            'post' => $post
        ]);
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
            'slug' => Str::slug($validated['title']) . '-' . now()->format('YmdHis')
        ]);
        return response()->json([
            'status' => 'success',
            'post' => $post
        ]);
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
        return response()->json([
            'status' => 'success',
            'post' => $post,
            'message' => 'Post updated successfully'
        ]);
    }
    public function softDelete(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        $trashed = Post::onlyTrashed()->get();
        return response()->json([
            'message' => 'Post deleted successfully',
            'trash_bin' => $trashed
        ]);
    }
    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $post);
        $post->restore();

        return response()->json([
            'status' => 'success',
            'mesage' => 'Post restore successfully',
            'post' => $post
        ]);
    }

}
