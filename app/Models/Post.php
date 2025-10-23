<?php

namespace App\Models;

use App\Models\Bookmark;
use App\Models\Like;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id','category_id','title','slug','content','status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    public function likedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    public function bookmark()
    {
        return $this->hasMany(Bookmark::class);
    }

}
