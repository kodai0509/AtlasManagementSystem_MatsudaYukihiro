<?php

namespace App\Http\Controllers\Authenticated\BulletinBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\MainCategory;
use App\Models\Categories\SubCategory;
use App\Models\Posts\Post;
use App\Models\Posts\PostComment;
use App\Models\Posts\Like;
use App\Http\Requests\BulletinBoard\PostFormRequest;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    // 投稿一覧
    public function show(Request $request)
    {
        $categories = MainCategory::with('subCategories')->get();

        $postsQuery = Post::with(['user', 'postComments'])->withCount('likes');

        // サブカテゴリー検索
        if ($request->filled('sub_category_id')) {
            $postsQuery->where('sub_category_id', $request->sub_category_id);
        }
        // キーワード検索
        elseif ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $subCategory = SubCategory::where('sub_category', $keyword)->first();

            if ($subCategory) {
                $postsQuery->where('sub_category_id', $subCategory->id);
            } else {
                $postsQuery->where(function ($q) use ($keyword) {
                    $q->where('post_title', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function ($userQ) use ($keyword) {
                            $userQ->where('over_name', 'like', "%{$keyword}%")
                                ->orWhere('under_name', 'like', "%{$keyword}%");
                        });
                });
            }
        }

        // いいねした投稿
        if ($request->filled('like_posts')) {
            $likePostIds = Auth::user()->likePostId()->pluck('like_post_id');
            $postsQuery->whereIn('id', $likePostIds);
        }

        // 自分の投稿
        if ($request->filled('my_posts')) {
            $postsQuery->where('user_id', Auth::id());
        }

        $posts = $postsQuery->get();

        return view('authenticated.bulletinboard.posts', compact('posts', 'categories'));
    }

    // 投稿詳細
    public function postDetail($post_id)
    {
        $post = Post::with(['user', 'postComments'])->withCount('likes')->findOrFail($post_id);
        return view('authenticated.bulletinboard.post_detail', compact('post'));
    }

    // 投稿作成フォーム
    public function postInput()
    {
        $main_categories = MainCategory::with('subCategories')->get();
        return view('authenticated.bulletinboard.post_create', compact('main_categories'));
    }

    // 投稿作成
    public function postCreate(PostFormRequest $request)
    {
        Post::create([
            'user_id'         => Auth::id(),
            'post_title'      => $request->post_title,
            'post'            => $request->post_body,
            'sub_category_id' => $request->sub_category_id,
        ]);

        return redirect()->route('post.show');
    }

    // 投稿編集
    public function postEdit(Request $request)
    {
        $request->validate([
            'post_title' => 'required|string|max:100',
            'post_body'  => 'required|string|max:2000',
        ]);

        $post = Post::where('id', $request->post_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $post->update([
            'post_title' => $request->post_title,
            'post'       => $request->post_body,
        ]);

        return redirect()->route('post.detail', ['id' => $post->id]);
    }

    // 投稿削除
    public function postDelete($id)
    {
        Post::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('post.show');
    }
}
