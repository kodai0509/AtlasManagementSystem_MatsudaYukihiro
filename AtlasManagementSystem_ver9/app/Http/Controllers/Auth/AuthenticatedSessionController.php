<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $userdata = $request->only('mail_address', 'password');
        if (Auth::attempt($userdata)) {
            return redirect('top');
        } else {
            return redirect('login')->with('flash_message', 'name or password is incorrect');
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('login');
    }

    public function registerPost(Request $request)
    {
        // バリデーション処理
        $validated = $request->validate([
            'over_name' => 'required|string|max:255',
            'under_name' => 'required|string|max:255',
            'mail_address' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 他のフィールドのバリデーション
        ]);

        // ユーザー作成
        $user = User::create([
            'over_name' => $request->over_name,
            'under_name' => $request->under_name,
            'mail_address' => $request->mail_address,
            'password' => bcrypt($request->password),
            // 他のフィールドも保存
        ]);

        // 登録後、ログイン処理
        Auth::login($user);

        // ログイン後、ログイン画面にリダイレクト
        return redirect()->route('loginView');
    }
}
