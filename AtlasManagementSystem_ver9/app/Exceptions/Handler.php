<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        // ▼ TokenMismatchException（419）をログイン画面へリダイレクト
        if ($exception instanceof TokenMismatchException) {
            return redirect()
                ->route('loginView')
                ->with('message', 'セッションの有効期限が切れました。もう一度ログインしてください。');
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->route('loginView');
    }
}
