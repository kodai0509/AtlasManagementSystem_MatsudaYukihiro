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

        // サブカテゴリー検索(エラーはないが何も表示されない)
        if ($request->filled('sub_category_id')) {
            $subCategoryId = $request->input('sub_category_id');
            $postsQuery->whereHas('subCategories', function ($q) use ($subCategoryId) {
                $q->where('sub_categories.id', $subCategoryId);
            });
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

        $post = Post::create([
            'user_id'         => Auth::id(),
            'post_title'      => $request->post_title,
            'post'            => $request->post_body,
        ]);

        $subCategoryIds = is_array($request->sub_category_id) ? $request->sub_category_id : [$request->sub_category_id];
        $post->subCategories()->sync($subCategoryIds);

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

        $subCategoryIds = is_array($request->sub_category_id) ? $request->sub_category_id : [$request->sub_category_id];
        $post->subCategories()->sync($subCategoryIds);

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

    // メインカテゴリ作成
    public function mainCategoryCreate(Request $request)
    {
        MainCategory::create(['main_category' => $request->main_category_name]);
        return redirect()->route('post.input');
    }

    // コメント作成
    public function commentCreate(Request $request)
    {
        $request->validate([
            'comment' => 'required|string|max:250',
        ]);

        PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    // 自分の投稿一覧
    public function myBulletinBoard()
    {
        $posts = Auth::user()->posts()
            ->with(['user', 'postComments'])
            ->withCount('likes')
            ->get();

        $like = new Like;
        return view('authenticated.bulletinboard.post_myself', compact('posts', 'like'));
    }

    // いいねした投稿一覧
    public function likeBulletinBoard()
    {
        $likePostIds = Like::where('like_user_id', Auth::id())->pluck('like_post_id');

        $posts = Post::with(['user', 'postComments'])
            ->whereIn('id', $likePostIds)
            ->withCount('likes')
            ->get();

        $like = new Like;
        return view('authenticated.bulletinboard.post_like', compact('posts', 'like'));
    }

    // いいね処理
    public function postLike(Request $request)
    {
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $alreadyLiked = Like::where('like_user_id', $user_id)
            ->where('like_post_id', $post_id)
            ->exists();

        if (!$alreadyLiked) {
            Like::create([
                'like_user_id' => $user_id,
                'like_post_id' => $post_id,
            ]);
        }

        $likeCount = Like::where('like_post_id', $post_id)->count();

        return response()->json(['like_count' => $likeCount]);
    }

    // いいね解除処理
    public function postUnLike(Request $request)
    {
        Like::where('like_user_id', Auth::id())
            ->where('like_post_id', $request->post_id)
            ->delete();

        $likeCount = Like::where('like_post_id', $request->post_id)->count();

        return response()->json(['like_count' => $likeCount]);
    }
}
