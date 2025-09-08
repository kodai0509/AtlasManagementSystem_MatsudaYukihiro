<x-sidebar>
  <div class="board_area w-100 border d-flex">
    <div class="post_view w-75 mt-5">
      @foreach($posts as $post)
      <div class="post_area border w-75 m-auto p-3">
        <p><span>{{ $post->user->over_name }}</span><span class="ml-3">{{ $post->user->under_name }}</span>さん</p>

        <!-- 投稿タイトル -->
        <a href="{{ route('post.detail', ['id' => $post->id]) }}"
          style="font-weight: bold; color: black; text-decoration: none; display: inline-block; margin-bottom: 6px;">
          {{ $post->post_title }}
        </a>

        <div class="post_bottom d-flex justify-content-between align-items-center">
          <!-- サブカテゴリー -->
          <div class="d-flex flex-wrap" style="gap: 5px;">
            @if($post->subCategories->isNotEmpty())
            @foreach($post->subCategories as $subCategory)
            <span class="sub-category-badge">{{ $subCategory->sub_category }}</span>
            @endforeach
            @endif
          </div>

          <!-- いいね・コメント -->
          <div class="d-flex" style="gap: 15px; white-space: nowrap; align-items: center;">
            <div style="display: flex; align-items: center;">
              <i class="fa fa-comment"></i><span>{{ $post->commentCount() }}</span>
            </div>
            <div style="display: flex; align-items: center;">
              @if(Auth::user()->is_Like($post->id))
              <p class="m-0" style="display: flex; align-items: center;">
                <i class="fas fa-heart un_like_btn" post_id="{{ $post->id }}"></i>
                <span class="like_counts{{ $post->id }}" style="margin-left: 4px;">{{ $post->likes_count ?? 0 }}</span>
              </p>
              @else
              <p class="m-0" style="display: flex; align-items: center;">
                <i class="fas fa-heart like_btn" post_id="{{ $post->id }}"></i>
                <span class="like_counts{{ $post->id }}" style="margin-left: 4px;">{{ $post->likes_count ?? 0 }}</span>
              </p>
              @endif
            </div>
          </div>
        </div>
      </div>

      @endforeach
    </div>

    <div class="other_area w-25">
      <div class="m-4">
        <div class="post_btn"><a href="{{ route('post.input') }}" style="color: #FFFFFF;">投稿 </a>
        </div>
        <!-- 検索フォーム -->
        <form action="{{ route('post.show') }}" method="get" id="postSearchRequest">
          <div class="search_area">
            <input type="text" placeholder="キーワードを検索" name="keyword" value="{{ request('keyword') }}">
            <input type="submit" value="検索">
          </div>
          <div class="search_posts">
            <input type="submit" name="like_posts" class="category_btn like_posts" value="いいねした投稿">
            <input type="submit" name="my_posts" class="category_btn my_posts" value="自分の投稿">
          </div>
        </form>
        <div class="category_select_area">
          <label>カテゴリー検索</label>
          @foreach($categories as $category)
          <div class="conditions_wrapper">
            <p class="main_categories m-0" category_id="{{ $category->id }}">
              {{ $category->main_category }}
            </p>
            <div class="category_num{{ $category->id }} sub_categories_list">
              <ul>
                @foreach($category->subCategories as $sub)
                <li>
                  <span>{{ $sub->sub_category }}</span>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>
</x-sidebar>
