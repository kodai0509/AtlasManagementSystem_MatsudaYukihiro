<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Models\Users\Subjects;
use App\Models\Users\User;

class RegisteredUserController extends Controller
{
    public function create()
    {
        $subjects = Subjects::all();
        return view('auth.register.register', compact('subjects'));
    }

    public function store(Request $request)
    {
        // バリデーションルール
        $validated = $request->validate([
            'over_name' => ['required', 'string', 'max:10'],
            'under_name' => ['required', 'string', 'max:10'],
            'over_name_kana' => ['required', 'string', 'regex:/^[ァ-ヶー]+$/u', 'max:30'],
            'under_name_kana' => ['required', 'string', 'regex:/^[ァ-ヶー]+$/u', 'max:30'],
            'mail_address' => ['required', 'string', 'email', 'max:100', 'unique:users,mail_address'],
            'sex' => ['required', Rule::in([1, 2, 3])],
            'old_year' => ['required', 'integer', 'min:1900', 'max:' . Carbon::now()->year],
            'old_month' => ['required', 'integer', 'min:1', 'max:12'],
            'old_day' => ['required', 'integer', 'min:1', 'max:31'],
            'role' => ['required', Rule::in([1, 2, 3, 4])],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:30'],
        ], [
            'old_year.min' => '生年月日の年は1900年以降で入力してください。',
            'old_year.max' => '生年月日の年は現在の年を超えることはできません。',
            'old_month.min' => '月は1から12の間で入力してください。',
            'old_day.min' => '日付は1から31の間で入力してください。',
            'password.confirmed' => 'パスワード確認が一致しません。',
        ]);

        // 日付チェック
        if (!checkdate($request->old_month, $request->old_day, $request->old_year)) {
            return back()->withErrors(['birth_day' => '正しい日付を入力してください。'])->withInput();
        }

        $birth_day = Carbon::createFromDate($request->old_year, $request->old_month, $request->old_day);
        $min_date = Carbon::create(2000, 1, 1);
        $max_date = Carbon::today();

        if ($birth_day->lt($min_date) || $birth_day->gt($max_date)) {
            return back()->withErrors(['birth_day' => '生年月日は2000年1月1日から今日までの間で入力してください。'])->withInput();
        }

        // ユーザー作成
        DB::beginTransaction();
        try {
            $user_get = User::create([
                'over_name' => $request->over_name,
                'under_name' => $request->under_name,
                'over_name_kana' => $request->over_name_kana,
                'under_name_kana' => $request->under_name_kana,
                'mail_address' => $request->mail_address,
                'sex' => $request->sex,
                'birth_day' => $birth_day,
                'role' => $request->role,
                'password' => bcrypt($request->password)
            ]);

            dd($request->subject);

            if ($request->role == 4) {
                $user = User::findOrFail($user_get->id);

                // 重複を排除して科目を関連付ける
                $user->subjects()->sync($request->subject);
            }

            Auth::login($user_get);
            DB::commit();

            return redirect()->route('login')->with('flash_message', '登録が完了しました。ログインしてください。');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('loginView')->withErrors(['error' => '登録中にエラーが発生しました。']);
        }
    }
}
