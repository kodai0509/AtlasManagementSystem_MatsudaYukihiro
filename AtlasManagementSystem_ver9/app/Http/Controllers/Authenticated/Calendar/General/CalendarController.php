<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Calendars\Calendar;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function show()
    {
        $user = \App\Models\Users\User::with('reserveSettings')->find(Auth::id());
        $userId = Auth::id();
        $calendar = new CalendarView(time(), $userId);

        // 予約済みのReserveSettingsを取得
        $userReserved = $user->reserveSettings()->get();

        return view('authenticated.calendar.general.calendar', compact('calendar', 'userReserved'));
    }



    public function reserve(Request $request)
    {
        DB::beginTransaction();
        try {
            $reserveParts = $request->input('reserve_parts', []);  // ← 修正ポイント

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

                $reserveSetting->decrement('limit_users');
                $reserveSetting->users()->attach(Auth::id());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => '予約に失敗しました: ' . $e->getMessage()]);
        }

        return redirect()->route('calendar.general.show');
    }


    public function cancel(Request $request)
    {
        $user = Auth::user();
        $date = $request->reserve_date;
        $partName = $request->reserve_part;


        preg_match('/リモ(\d+)部/', $partName, $matches);
        if (!isset($matches[1])) {
            return back()->withErrors(['error' => 'キャンセル部位が不正です']);
        }
        $part = $matches[1];

        $reserveSetting = ReserveSettings::where('setting_reserve', $date)
            ->where('setting_part', $part)
            ->first();

        if (!$reserveSetting) {
            return back()->withErrors(['error' => '予約設定が見つかりません']);
        }

        $reserveSetting->users()->detach($user->id);

        $reserveSetting->increment('limit_users');

        return redirect()->route('calendar.general.show')->with('status', '予約をキャンセルしました');
    }
}
