<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;

class BookmarkController extends Controller
{
    public function toggleBookmark(int $post_id)
    {
        $user = auth()->user();

        $bookmark = Bookmark::firstWhere([
            'user_id' => $user->id,
            'post_id' => $post_id,
        ]);

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
        } else {
            $bookmark = Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post_id,
            ]);
            $bookmarked = true;
        }

        return $this->sendResponse([
            'bookmarked' => $bookmarked,
            'bookmark' => $bookmarked ? new BookmarkResource($bookmark) : null,
        ], $bookmarked
            ? 'Post has been added to bookmarks.'
            : 'Post has been removed from bookmarks.');
    }

    public function index()
    {
        $bookmarks = Bookmark::where('user_id', auth()->id())->paginate(10);
        return $this->sendResponse([
            'bookmarks' => BookmarkResource::collection($bookmarks),
            'pagination' => [
                'total' => $bookmarks->total(),
                'per_page' => $bookmarks->perPage(),
                'current_page' => $bookmarks->currentPage(),
                'last_page' => $bookmarks->lastPage(),
            ],
        ], 'Bookmarks retrieved successfully');
    }
}
