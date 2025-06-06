<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'post_title',
        'post',
        'sub_category_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Users\User');
    }

    public function postComments()
    {
        return $this->hasMany('App\Models\Posts\PostComment');
    }

    public function subCategories()
    {
        // リレーションの定義
    }

    // コメント数
    public function commentCount()
    {
        return $this->postComments()->count();
    }
    // いいね数
    public function likes()
    {
        return $this->hasMany(Like::class, 'like_post_id');
    }

    public function likeCount()
    {
        return $this->likes()->count();
    }
}
