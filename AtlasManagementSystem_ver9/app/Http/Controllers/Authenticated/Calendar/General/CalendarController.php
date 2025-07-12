<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function show()
    {
        $user = User::with('reserveSettings')->find(Auth::id());
        $userId = Auth::id();
        $calendar = new CalendarView(time(), $userId);

        $userReserved = $user ? $user->reserveSettings()->get() : collect();

        return view('authenticated.calendar.general.calendar', compact('calendar', 'userReserved'));
    }

    public function reserve(Request $request)
    {
        // \Log::debug('All request data:', $request->all());
        // \Log::debug('Reserve parts:', $request->input('reserve_parts', []));

        // DB::beginTransaction();
        try {
            $reserveParts = $request->input('reserve_parts', []);

            if (empty($reserveParts)) {
                throw new \Exception('予約データがありません');
            }

            foreach ($reserveParts as $date => $part) {
                if (empty($part)) {
                    continue;
                }

                $reserveSetting = ReserveSettings::where('setting_reserve', $date)
                    ->where('setting_part', $part)
                    ->first();

                if (!$reserveSetting) {
                    throw new \Exception("予約設定が見つかりません: $date 部 $part");
                }

                // 予約済みか
                if ($reserveSetting->users()->where('user_id', Auth::id())->exists()) {
                    // Log::info('すでに予約済みのためスキップ', ['user_id' => Auth::id(), 'date' => $date, 'part' => $part]);
                    continue;
                }

                // 残り枠があるか
                $reservedCount = $reserveSetting->users()->count();
                $remaining = $reserveSetting->limit_users - $reservedCount;
                if ($remaining <= 0) {
                    throw new \Exception("予約枠が満員です: $date 部 $part");
                }

                $reserveSetting->users()->attach(Auth::id(), [
                    'created_at' => now()
                ]);
            }

            // DB::commit();
            return redirect()->route('calendar.general.show', ['user_id' => Auth::id()])
                ->with('success', '予約が完了しました');
        } catch (\Exception $e) {
            // DB::rollback();
            // \Log::error('予約エラー:', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return back()->withErrors(['error' => '予約に失敗しました: ' . $e->getMessage()]);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $user = Auth::user();
            $reserveId = $request->input('reserve_id');

            $reserveSetting = ReserveSettings::find($reserveId);

            if (!$reserveSetting) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => '予約設定が見つかりません']);
                }
                return back()->withErrors(['error' => '予約設定が見つかりません']);
            }

            if (!$reserveSetting->users()->where('user_id', $user->id)->exists()) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'この予約は存在しません']);
                }
                return back()->withErrors(['error' => 'この予約は存在しません']);
            }

            $reserveSetting->users()->detach($user->id);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => '予約をキャンセルしました']);
            }

            return redirect()->route('calendar.general.show')
                ->with('status', '予約をキャンセルしました');
        } catch (\Exception $e) {
            Log::error('予約キャンセルエラー:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'キャンセルに失敗しました']);
            }

            return back()->withErrors(['error' => 'キャンセルに失敗しました']);
        }
    }
}
