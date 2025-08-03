<x-sidebar>
  <div class="post_create_container d-flex">
    <div class="post_create_area border w-75 m-5 p-5">
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
    <div class="w-45 ml-auto mr-auto">
      <div class="category_area mt-5 p-5" style="margin-right: 20px;">
        <form action=" {{ route('main.category.create') }}" method="post" id="mainCategoryRequest">
          @csrf
          @if($errors->has('main_category_name'))
          <span class="text-danger" style="white-space: nowrap;">{{ $errors->first('main_category_name') }}</span>
          @endif
          <p class="m-0">メインカテゴリー</p>
          <input type="text" class="w-100" name="main_category_name">
          <input type="submit" value="追加" class="w-100 btn btn-primary p-0">
        </form>

        @if (Auth::user()->role === 1)
        <!-- サブカテゴリー -->
        <div class="mt-5">
          @if ($errors->has('sub_category_name'))
          <span class="text-danger" style="white-space: nowrap;">{{ $errors->first('sub_category_name') }}</span>
          @endif
          <p class="m-0">サブカテゴリー</p>
          <select name="main_category_id" class="category_input" form="subCategoryRequest">
            <option selected disabled>----</option>
            @foreach($main_categories as $main_category)
            <option value="{{ $main_category->id }}">
              {{ $main_category->main_category }}
            </option>
            @endforeach
          </select>
          <input type="text" name="sub_category_name" class="category_input" form="subCategoryRequest">
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
