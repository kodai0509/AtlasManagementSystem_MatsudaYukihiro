<x-guest-layout>
  <form action="{{ route('registerPost') }}" method="POST" style="padding-top: 100px;padding-bottom: 100px; background-color:#ECF1F6;">
    {{ csrf_field() }}
    <div class="w-100 vh-100 d-flex" style="align-items:center; justify-content:center;">
      <div class="w-30 vh-75 border p-3" style="background-color:white; position: relative;">
        <div class="register_form">

          <div class="d-flex mt-3" style="justify-content:space-between;">
            <div class="" style="width:200px">
              @error('over_name')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
              <label class="d-block m-0" style="font-size:13px">姓</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 over_name" name="over_name">
              </div>
            </div>
            <div class="" style="width:140px">
              @error('under_name')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
              <label class=" d-block m-0" style="font-size:13px">名</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 under_name" name="under_name">
              </div>
            </div>
          </div>
          <div class="d-flex mt-3" style="justify-content:space-between">
            <div class="" style="width:140px">
              <!-- @error('over_name_kana')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror -->
              <label class="d-block m-0" style="font-size:13px">セイ</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 over_name_kana" name="over_name_kana">
              </div>
            </div>
            <div class="" style="width:140px">
              <!-- @error('under_name_kana')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror -->
              <label class="d-block m-0" style="font-size:13px">メイ</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 under_name_kana" name="under_name_kana">
              </div>
            </div>
          </div>

          <div class="mt-3">
            @error('mail_address')
            <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
            @enderror
            <label class="m-0 d-block" style="font-size:13px">メールアドレス</label>
            <div class="border-bottom border-primary">
              <input type="email" class="w-100 border-0 mail_address" name="mail_address">
            </div>
          </div>
        </div>

        <div class="mt-3 text-center">
          @error('sex')
          <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
          @enderror
          <label style="font-size:13px; margin-right:20px;">
            <input type="radio" name="sex" class="sex" value="1"> 男性
          </label>
          <label style="font-size:13px; margin-right:20px;">
            <input type="radio" name="sex" class="sex" value="2"> 女性
          </label>
          <label style="font-size:13px;">
            <input type="radio" name="sex" class="sex" value="3"> その他
          </label>
        </div>

        <div class="mt-3" style="position: relative;">
          @error('old_month')
          <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
          @enderror
          @error('birth_day')
          <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
          @enderror
          <label class="d-block m-0" style="font-size:13px">生年月日</label>
          <div class="border-bottom border-primary">
            <div style="display: flex; flex-wrap: nowrap; align-items: flex-start; gap: 10px;">
              <!-- 年 -->
              <div style="display: flex; flex-direction: column; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 4px;">
                  <!-- @error('old_year')
                <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
                @enderror -->
                  <select class="old_year" name="old_year" style="min-width: 90px; border: none; outline: none;">
                    <option value="none">-----</option>
                    @for ($y = 2000; $y <= 2025; $y++)
                      <option value="{{ $y }}">{{ $y }}</option>
                      @endfor
                  </select>
                  <span style="font-size:13px;">年</span>
                </div>
              </div>

              <!-- 月  -->
              <div style="display: flex; flex-direction: column; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 4px;">
                  <!-- @error('old_year')
                <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
                @enderror -->
                  <select class="old_month" name="old_month" style="min-width: 60px; border: none; outline: none;">
                    <option value="none">--</option>
                    @for ($m = 1; $m <= 12; $m++)
                      <option value="{{ $m }}">{{ $m }}</option>
                      @endfor
                  </select>
                  <span style="font-size:13px;">月</span>
                </div>
              </div>
              <!-- 日 -->
              <div style="display: flex; flex-direction: column; align-items: flex-start;">
                <!-- @error('old_day')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror -->
                <div style="display: flex; align-items: center; gap: 4px;">
                  <select class="old_day" name="old_day" style="min-width: 60px; border: none; outline: none;">
                    <option value="none">--</option>
                    @for ($d = 1; $d <= 31; $d++)
                      <option value="{{ $d }}">{{ $d }}</option>
                      @endfor
                  </select>
                  <span style="font-size:13px;">日</span>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-3" style="gap: 10px;">
            @error('role')
            <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
            @enderror

            <label class="d-block m-0" style="font-size:13px">役職</label>

            <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
              <label style="font-size:13px;">
                <input type="radio" name="role" class="admin_role role" value="1">
                教師(国語)
              </label>
              <label style="font-size:13px;">
                <input type="radio" name="role" class="admin_role role" value="2">
                教師(数学)
              </label>
              <label style="font-size:13px;">
                <input type="radio" name="role" class="admin_role role" value="3">
                教師(英語)
              </label>
              <label style="font-size:13px;" class="other_role">
                <input type="radio" name="role" class="other_role role" value="4">
                生徒
              </label>
            </div>
          </div>


          <div class="select_teacher d-none">
            <label class="d-block m-0" style="font-size:13px">選択科目</label>
            @foreach($subjects as $subject)
            <div class="">
              <input type="checkbox" name="subjects[]" value="{{ $subject->id }}">
              <label>{{ $subject->subject }}</label>
            </div>
            @endforeach
          </div>

          <div class="mt-3">
            <label class="d-block m-0" style="font-size:13px">パスワード</label>
            @error('password')
            <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
            @enderror
            <div class="border-bottom border-primary">
              <input type="password" class="border-0 w-100 password" name="password">
            </div>
          </div>

          <div class="mt-3">
            @error('password_confirmation')
            <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
            @enderror
            <label class="d-block m-0" style="font-size:13px">確認用パスワード</label>
            <div class="border-bottom border-primary">
              <input type="password" class="border-0 w-100 password_confirmation" name="password_confirmation">
            </div>
          </div>
          <div class="text-right" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">新規登録</button>
          </div>
          <div class="text-center">
            <a href="{{ route('loginView') }}">ログインはこちら</a>
          </div>
        </div>
      </div>
  </form>
</x-guest-layout>
