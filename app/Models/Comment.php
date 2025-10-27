<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    // والد
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function likesCount()
    {
        return $this->likes()->where('is_like', true)->count();
    }

    public function dislikesCount()
    {
        return $this->likes()->where('is_like', false)->count();
    }

}
