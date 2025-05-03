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
            'old_year' => ['required', 'integer', 'min:2000', 'max:' . Carbon::now()->year, 'not_in:none'],
            'old_month' => ['required', 'integer', 'min:1', 'max:12', 'not_in:none'],
            'old_day' => ['required', 'integer', 'min:1', 'max:31', 'not_in:none'],
            'role' => ['required', Rule::in([1, 2, 3, 4])],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:30'],
        ], [
            'over_name.required' => '入力必須です。',
            'over_name.max' => '10文字以下で入力してください。',
            'under_name.required' => '入力必須です。',
            'under_name.max' => '10文字以下で入力してください。',
            'over_name_kana.required' => '入力必須です。',
            'over_name_kana.regex' => 'カタカナで入力してください',
            'over_name_kana.max' => '30文字以下で入力してください。',
            'under_name_kana.required' => '入力必須です。',
            'under_name_kana.regex' => 'カタカナで入力してください',
            'under_name_kana.max' => '30文字以下で入力してください。',
            'mail_address.required' => '入力必須です。',
            'mail_address.email' => 'メール形式で入力してください。',
            'mail_address.max' => '100文字以下で入力してください。',
            'mail_address.unique' => '登録済みのメールアドレスです',
            'sex.required' => '入力必須です。',
            'old_year.required' => '入力必須です。',
            'old_year.min' => '2000年以降を入力してください。',
            'old_year.max' => '未来の日付は選べません。',
            'old_month.required' => '入力必須です。',
            'old_month.min' => '月は1以上で入力してください。',
            'old_month.max' => '月は12以下で入力してください。',
            'old_day.required' => '入力必須です。',
            'old_day.min' => '日付は1以上で入力してください。',
            'old_day.max' => '日付は31以下で入力してください。',
            'old_year.not_in' => '年を選択してください。',
            'old_month.not_in' => '月を選択してください。',
            'old_day.not_in' => '日を選択してください。',
            'role.required' => '入力必須です。',
            'password.required' => '入力必須です。',
            'password.min' => '8文字以上で入力してください。',
            'password.max' => '30文字以下で入力してください。',
            'password.confirmed' => 'パスワード確認が一致しません。',
        ]);

        // 日付チェック
        $year = (int)$request->input('old_year');
        $month = (int)$request->input('old_month');
        $day = (int)$request->input('old_day');

        if (!checkdate($month, $day, $year)) {
            return back()
                ->withErrors(['old_day' => '存在しない日付です。'])
                ->withInput();
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

            if ($request->role == 4) {
                // 重複している科目を取り除くために配列をユニークにする
                $uniqueSubjects = array_unique($request->subject);

                // ユーザーと科目を関連付け
                $user = User::findOrFail($user_get->id);
                $user->subjects()->sync($uniqueSubjects);
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
