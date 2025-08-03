<x-sidebar>
  <div class="vh-100 d-flex">
    <div class="w-50 mt-5">
      <div class="m-3 detail_container">
        <div class="p-3">
          <div class="detail_inner_head">
            <div>
            </div>
            <!-- 削除モーダル -->
            <div class="modal delete-modal js-delete-modal" style="display: none;">
              <div class="modal__bg js-delete-modal-close"></div>
              <div class="modal__content">
                <form method="POST" action="" class="delete-form">
                  @csrf
                  @method('DELETE')
                  <div class="w-100">
                    <div class="modal-inner-title w-50 m-auto text-center mb-3">
                      <p>この投稿を削除しますか？</p>
                    </div>
                    <div class="w-50 m-auto d-flex justify-content-between">
                      <button type="button" class="btn btn-primary js-delete-modal-close">キャンセル</button>
                      <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="contributor d-flex justify-content-between align-items-center">
            <!--投稿タイトル -->
            <div class="my-post">
              @foreach($post->subCategories as $subCategory)
              <div class="post_title_oval" style="background-color: #03aad2; color: white; border-radius: 50px; padding: 8px 20px; margin-bottom: 10px; display: inline-block; font-weight: bold;">
                {{ $subCategory->sub_category }}
              </div>
              @endforeach
            </div>

            <!-- 編集・削除ボタン -->
            @if (Auth::id() === $post->user_id)
            <div class="post-actions">
              <button type="button" class="btn btn-primary edit-modal-open"
                data-post-title="{{ $post->post_title }}"
                data-post-body="{{ $post->post }}"
                data-post-id="{{ $post->id }}">
                編集
              </button>
              <button class="btn btn-danger delete-modal-open"
                data-post-id="{{ $post->id }}"
                data-delete-url="{{ route('posts.delete', $post->id) }}">
                削除
              </button>
            </div>
            @endif
          </div>
          <div class="detail_post_title">{{ $post->post_title }}</div>
          <div class="mt-3 detail_post">{{ $post->post }}</div>
        </div>
        <div class="p-3">
          <div class="p-3">
            <div class="comment_container">
              <span class="">コメント ({{ $post->postComments->count() }}件)</span>
              @foreach($post->postComments as $comment)
              <div class="comment_area border-top">
                <p>
                  <span>{{ $comment->commentUser($comment->user_id)->over_name }}</span>
                  <span>{{ $comment->commentUser($comment->user_id)->under_name }}</span>さん
                </p>
                <p>{{ $comment->comment }}</p>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="w-50 p-3">
      <div class="comment_container border m-5">
        <div class="comment_area p-3">
          <p class="m-0">コメントする</p>
          <!-- バリデーションエラー -->
          @if ($errors->has('comment'))
          <div class="alert alert-danger">
            @foreach ($errors->get('comment') as $message)
            <p>{{ $message }}</p>
            @endforeach
          </div>
          @endif
          <textarea class="w-100" name="comment" form="commentRequest"></textarea>
          <input type="hidden" name="post_id" form="commentRequest" value="{{ $post->id }}">
          <input type="submit" class="btn btn-primary" form="commentRequest" value="投稿">
          <form action="{{ route('comment.create') }}" method="post" id="commentRequest">{{ csrf_field() }}</form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal js-modal">
    <div class="modal__bg js-modal-close"></div>
    <div class="modal__content">
      <form action="{{ route('post.edit') }}" method="post">
        {{ csrf_field() }}
        <div class="w-100">
          <div class="modal-inner-title w-50 m-auto">
            <input type="text" name="post_title" placeholder="タイトル" class="w-100" value="">
          </div>
          <div class="modal-inner-body w-50 m-auto pt-3 pb-3">
            <textarea placeholder="投稿内容" name="post_body" class="w-100"></textarea>
          </div>
          <div class="w-50 m-auto edit-modal-btn d-flex">
            <a class="js-modal-close btn btn-danger d-inline-block" href="#">閉じる</a>
            <input type="hidden" class="edit-modal-hidden" name="post_id" value="">
            <input type="submit" class="btn btn-primary d-block" value="編集">
          </div>
        </div>
      </form>
    </div>
  </div>

</x-sidebar>
