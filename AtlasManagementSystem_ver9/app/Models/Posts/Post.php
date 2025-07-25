<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Model;
use App\Models\Posts\Like;
use App\Models\Categories\SubCategory;

class Post extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'post_title',
        'post',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Users\User');
    }

    public function postComments()
    {
        return $this->hasMany('App\Models\Posts\PostComment');
    }

    public function commentCount()
    {
        return $this->postComments()->count();
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'like_post_id');
    }

    public function likeCount()
    {
        return $this->likes()->count();
    }

    // 投稿からサブカテゴリーを抽出
    public function getSubCategoriesAttribute()
    {
        $categories = [];

        // 本文からカテゴリー情報を抽出
        if (preg_match_all('/#Category:([^\n\r]+)/', $this->post, $matches)) {
            foreach ($matches[1] as $categoryName) {
                $subCategory = SubCategory::where('sub_category', trim($categoryName))->first();
                if ($subCategory) {
                    $categories[] = $subCategory;
                }
            }
        }

        // タイトルからカテゴリー情報を抽出
        if (preg_match_all('/\[([^\]]+)\]/', $this->post_title, $matches)) {
            foreach ($matches[1] as $categoryName) {
                $subCategory = SubCategory::where('sub_category', trim($categoryName))->first();
                if ($subCategory && !in_array($subCategory, $categories)) {
                    $categories[] = $subCategory;
                }
            }
        }

        return collect($categories);
    }

    // 表示用の本文（カテゴリー情報を除去）
    public function getCleanPostAttribute()
    {
        $cleanPost = $this->post;

        // カテゴリー情報を除去
        $cleanPost = preg_replace('/#Category:[^\n\r]+\n*/', '', $cleanPost);

        return trim($cleanPost);
    }
}
