<x-sidebar>
  <!-- <p>ユーザー検索</p> 後で削除 -->
  <div class="search_content w-100 d-flex">
    <div class="reserve_users_area">
      @foreach($users as $user)
      <div class="one_person">
        <div>
          <span>ID : </span><span>{{ $user->id }}</span>
        </div>
        <div><span>名前 : </span>
          <a href="{{ route('user.profile', ['id' => $user->id]) }}">
            <span>{{ $user->over_name }}</span>
            <span>{{ $user->under_name }}</span>
          </a>
        </div>
        <div>
          <span>カナ : </span>
          <span>({{ $user->over_name_kana }}</span>
          <span>{{ $user->under_name_kana }})</span>
        </div>
        <div>
          @if($user->sex == 1)
          <span>性別 : </span><span>男</span>
          @elseif($user->sex == 2)
          <span>性別 : </span><span>女</span>
          @else
          <span>性別 : </span><span>その他</span>
          @endif
        </div>
        <div>
          <span>生年月日 : </span><span>{{ $user->birth_day }}</span>
        </div>
        <div>
          @if($user->role == 1)
          <span>権限 : </span><span>教師(国語)</span>
          @elseif($user->role == 2)
          <span>権限 : </span><span>教師(数学)</span>
          @elseif($user->role == 3)
          <span>権限 : </span><span>講師(英語)</span>
          @else
          <span>権限 : </span><span>生徒</span>
          @endif
        </div>
        <div>
          @if($user->role == 4)
          <span>選択科目 :</span>
          @if($user->subjects->isEmpty())
          <span>未登録</span>
          @else
          @foreach($user->subjects as $subject)
          <span>{{ $subject->subject }}</span>
          @endforeach
          @endif
          @endif
        </div>
      </div>
      @endforeach
    </div>
    <div class="search_area w-25">
      <div class="user-search">
        <lavel>検索</lavel>
        <div class="search-field">
          <input type="text" class="free_word" name="keyword" placeholder="キーワードを検索" form="userSearchRequest">
        </div>
        <div class="search-field">
          <lavel>カテゴリ</lavel>
          <select form="userSearchRequest" name="category">
            <option value="name">名前</option>
            <option value="id">社員ID</option>
          </select>
        </div>
        <div class="search-field">
          <label>並び替え</label>
          <select name="updown" form="userSearchRequest">
            <option value="ASC">昇順</option>
            <option value="DESC">降順</option>
          </select>
        </div>
        <div class="conditions_wrapper">
          <div class="search_span">
            <p class="m-0 search_conditions"><span>検索条件の追加</span></p>
          </div>
          <div class="search_conditions_inner">
            <div style="margin-bottom: 10px;">
              <label style="display: block; margin-bottom: 5px;">性別</label>
              <div style="display: flex; gap: 10px; align-items: center;">
                <label><input type="radio" name="sex" value="1" form="userSearchRequest"> 男</label>
                <label><input type="radio" name="sex" value="2" form="userSearchRequest"> 女</label>
                <label><input type="radio" name="sex" value="3" form="userSearchRequest"> その他</label>
              </div>
            </div>
            <div style="margin-bottom: 10px;">
              <label>権限</label>
              <select name="role" form="userSearchRequest" class="engineer">
                <option selected disabled>----</option>
                <option value="1">教師(国語)</option>
                <option value="2">教師(数学)</option>
                <option value="3">教師(英語)</option>
                <option value="4" class="">生徒</option>
              </select>
            </div>
            <div class="selected_engineer" style="margin-bottom: 10px;">
              <label>選択科目</label>
              <div class="subject-checkboxes" style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($subjects as $subject)
                <label style="display: flex; align-items: center;">
                  <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" form="userSearchRequest">
                  <span style="margin-left: 4px;">{{ $subject->subject }}</span>
                </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <div style="text-align: center; margin-top: 10px;">
          <input type="submit" name="search_btn" value="検索" form="userSearchRequest" style="width: 200px; background-color:#03aad2;">
        </div>
        <div style="text-align: center;">
          <a href="#" onclick="document.getElementById('userSearchRequest').reset(); return false;" style="color: blue; text-decoration: none;">
            リセット
          </a>
        </div>
      </div>
      <form action="{{ route('user.show') }}" method="get" id="userSearchRequest"></form>
    </div>
  </div>
</x-sidebar>
