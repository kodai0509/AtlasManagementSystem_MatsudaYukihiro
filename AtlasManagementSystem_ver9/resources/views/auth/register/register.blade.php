<x-guest-layout>
  <form action="{{ route('registerPost') }}" method="POST" style="padding-top: 100px;padding-bottom: 100px; background-color:#ECF1F6;">
    {{ csrf_field() }}
    <div class="w-100 vh-100 d-flex" style="align-items:center; justify-content:center;">
      <div class="w-25 vh-75 border p-3" style="background-color:white; position: relative;">
        <div class="register_form">
          <div class="d-flex mt-3" style="justify-content:space-between">
            <div class="" style="width:140px">
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
              @error('over_name_kana')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
              <label class="d-block m-0" style="font-size:13px">セイ</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 over_name_kana" name="over_name_kana">
              </div>
            </div>
            <div class="" style="width:140px">
              @error('under_name_kana')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
              <label class="d-block m-0" style="font-size:13px">メイ</label>
              <div class="border-bottom border-primary" style="width:140px;">
                <input type="text" style="width:140px;" class="border-0 under_name_kana" name="under_name_kana">
              </div>
            </div>
          </div>

          <div class="mt-3">
            <label class="m-0 d-block" style="font-size:13px">メールアドレス</label>
            <div class="border-bottom border-primary">
              @error('mail_address')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
              <input type="email" class="w-100 border-0 mail_address" name="mail_address">
            </div>
          </div>
        </div>

        <div class="mt-3 text-center">
          <label style="font-size:13px; margin-right:20px;">
            @error('sex')
            <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
            @enderror
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
          <label class="d-block m-0" style="font-size:13px">生年月日</label>
          <div style="display: flex; flex-wrap: nowrap; align-items: flex-start; gap: 10px;">
            {{-- 年 --}}
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
              <div style="display: flex; align-items: center; gap: 4px;">
                <select class="old_year" name="old_year" style="min-width: 90px;">
                  <option value="none">-----</option>
                  @for ($y = 2000; $y <= 2025; $y++)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                <span style="font-size:13px;">年</span>
              </div>
              @error('old_year')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
            </div>

            {{-- 月 --}}
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
              <div style="display: flex; align-items: center; gap: 4px;">
                <select class="old_month" name="old_month" style="min-width: 60px;">
                  <option value="none">--</option>
                  @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ $m }}</option>
                    @endfor
                </select>
                <span style="font-size:13px;">月</span>
              </div>
              @error('old_month')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
            </div>

            {{-- 日 --}}
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
              <div style="display: flex; align-items: center; gap: 4px;">
                <select class="old_day" name="old_day" style="min-width: 60px;">
                  <option value="none">--</option>
                  @for ($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}">{{ $d }}</option>
                    @endfor
                </select>
                <span style="font-size:13px;">日</span>
              </div>
              @error('old_day')
              <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="mt-3">
          <label class="d-block m-0" style="font-size:13px">役職</label>
          <input type="radio" name="role" class="admin_role role" value="1">
          <label style="font-size:13px">教師(国語)</label>
          <input type="radio" name="role" class="admin_role role" value="2">
          <label style="font-size:13px">教師(数学)</label>
          <input type="radio" name="role" class="admin_role role" value="3">
          <label style="font-size:13px">教師(英語)</label>
          <input type="radio" name="role" class="other_role role" value="4">
          <label style="font-size:13px" class="other_role">生徒</label>
        </div>

        <div class="select_teacher d-none">
          <label class="d-block m-0" style="font-size:13px">選択科目</label>
          @foreach($subjects as $subject)
          <div class="">
            <input type="checkbox" name="subject[]" value="{{ $subject->id }}">
            <label>{{ $subject->subject }}</label>
          </div>
          @endforeach
        </div>

        <div class="mt-3">
          @error('password')
          <div class="text-danger" style="font-size:12px;">{{ $message }}</div>
          @enderror
          <label class="d-block m-0" style="font-size:13px">パスワード</label>
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

        <div class="mt-5 text-right">
          <button type="submit" class="btn btn-primary">新規登録</button>
        </div>
        <div class="text-center">
          <a href="{{ route('loginView') }}">ログイン</a>
        </div>
      </div>
    </div>
  </form>
</x-guest-layout>
