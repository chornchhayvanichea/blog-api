<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function toggleBookmark(int $post_id)
    {
        $user = auth()->user();
        $bookmark = Bookmark::withTrashed()->firstWhere([
            'user_id' => $user->id,
            'post_id' => $post_id
        ]);

        if ($bookmark) {
            if ($bookmark->trashed()) {
                $bookmark->restore();
                $bookmarked = true;
            } else {
                $bookmark->delete();
                $bookmarked = false;
            }
        } else {
            $bookmark = Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post_id
            ]);
            $bookmarked = true;
        }
        return response()->json([
         'bookmarked' => $bookmarked,
         'message' => $bookmarked
             ? 'Post has been added to bookmarks.'
             : 'Post has been removed from bookmarks.',
         'bookmark' => $bookmarked
             ? [
                 'id' => $bookmark->id,
                 'user_id' => $bookmark->user_id,
                 'post_id' => $bookmark->post_id,
             ]
             : null,
    ]);
    }
    public function index()
    {
        $bookmarks = Bookmark::where('user_id', auth()->id())->get();
        return response()->json([
            'bookmarks' => $bookmarks
        ]);
    }
}
