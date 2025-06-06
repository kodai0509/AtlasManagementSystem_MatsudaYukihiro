<x-sidebar>
  <div class="post_create_container d-flex">
    <div class="post_create_area border w-50 m-5 p-5">
      <form action="{{ route('post.create') }}" method="post" id="postCreate">
        @csrf
        <div>
          @if ($errors->has('post_category_id'))
          <span class="error_message text-danger">{{ $errors->first('post_category_id') }}</span>
          @endif
          <p class="mb-0">カテゴリー</p>
          <select class="w-100" name="sub_category_id">
            @foreach($main_categories as $main_category)
            <optgroup label="{{ $main_category->main_category }}" style="color: gray; background-color: lightgray;">
              @foreach($main_category->subCategories as $sub_category)
              <option value="{{ $sub_category->id }}" style="color: black;">
                {{ $sub_category->sub_category }}
              </option>
              @endforeach
            </optgroup>
            @endforeach
          </select>
        </div>
        <div class="mt-3">
          @if($errors->first('post_title'))
          <span class="error_message text-danger">{{ $errors->first('post_title') }}</span>
          @endif
          <p class="mb-0">タイトル</p>
          <input type="text" class="w-100" name="post_title" value="{{ old('post_title') }}">
        </div>
        <div class="mt-3">
          @if($errors->first('post_body'))
          <span class="error_message text-danger">{{ $errors->first('post_body') }}</span>
          @endif
          <p class="mb-0">投稿内容</p>
          <textarea class="w-100" name="post_body">{{ old('post_body') }}</textarea>
        </div>

        <div class="mt-3 text-right">
          <input type="submit" class="btn btn-primary" value="投稿">
        </div>
      </form>
    </div>

    @can('admin')
    <div class="w-25 ml-auto mr-auto">
      <div class="category_area mt-5 p-5">
        <div>
          <p class="m-0">メインカテゴリー</p>
          <input type="text" class="w-100" name="main_category_name" form="mainCategoryRequest">
          <input type="submit" value="追加" class="w-100 btn btn-primary p-0" form="mainCategoryRequest">
        </div>

        @if (Auth::user()->role === 1)
        <div class="mt-5">
          <p class="m-0">サブカテゴリー</p>

          <select name="main_category_id" class="w-100 mb-2" form="subCategoryRequest">
            @foreach($main_categories as $main_category)
            <option value="{{ $main_category->id }}">{{ $main_category->main_category }}</option>
            @endforeach
          </select>

          <!-- サブカテゴリー -->
          <input type="text" class="w-100 mb-2" name="sub_category_name" form="subCategoryRequest" placeholder="サブカテゴリー名">

          @if ($errors->has('sub_category_name'))
          <span class="text-danger">{{ $errors->first('sub_category_name') }}</span>
          @endif

          <input type="submit" value="追加" class="w-100 btn btn-primary p-0" form="subCategoryRequest">

          <form action="{{ route('sub.category.create') }}" method="post" id="subCategoryRequest">
            @csrf
          </form>
        </div>
        @endif

        <form action="{{ route('main.category.create') }}" method="post" id="mainCategoryRequest">
          @csrf
        </form>

      </div>
    </div>
    @endcan
  </div>
</x-sidebar>
