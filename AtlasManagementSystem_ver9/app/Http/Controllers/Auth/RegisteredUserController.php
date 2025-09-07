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
        // 日本語メッセージにするため
        $request->merge([
            'old_year' => $request->old_year === 'none' ? null : $request->old_year,
            'old_month' => $request->old_month === 'none' ? null : $request->old_month,
            'old_day' => $request->old_day === 'none' ? null : $request->old_day,
        ]);

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
            'password_confirmation' => ['required', 'string', 'min:8', 'max:30'],
            'birth_day' => [
                function ($attribute, $value, $fail) use ($request) {
                    $year = (int)$request->old_year;
                    $month = (int)$request->old_month;
                    $day = (int)$request->old_day;

                    if (!checkdate($month, $day, $year)) {
                        $fail('存在しない日付です。');
                        return;
                    }

                    $birth_day = Carbon::createFromDate($year, $month, $day);
                    $min_date = Carbon::create(2000, 1, 1);
                    $max_date = Carbon::today();

                    if ($birth_day->lt($min_date) || $birth_day->gt($max_date)) {
                        $fail('生年月日は2000年1月1日から今日までの間で入力してください。');
                    }
                }
            ],
        ]);

        $year = (int)$request->old_year;
        $month = (int)$request->old_month;
        $day = (int)$request->old_day;
        if (!checkdate($month, $day, $year)) {
            return back()
                ->withErrors(['birth_day' => '存在しない日付です。'])
                ->withInput();
        }

        $birth_day = Carbon::createFromDate($year, $month, $day);

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
                $uniqueSubjects = array_unique($request->input('subjects', []));

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
