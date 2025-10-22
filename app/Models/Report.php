<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','reason'
    ];
    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function reportable()
    {
        return $this->morphTo();
    }
}
