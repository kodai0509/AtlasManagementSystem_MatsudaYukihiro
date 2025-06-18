<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Calendars\Calendar;
use App\Models\USers\User;
use Auth;
use DB;

class CalendarController extends Controller
{
    public function show()
    {
        $calendar = new CalendarView(time());
        return view('authenticated.calendar.general.calendar', compact('calendar'));
    }

    public function reserve(Request $request)
    {
        // Log::info('reserve called', $request->all());

        DB::beginTransaction();
        try {
            $getPart = $request->getPart ?? [];
            $getDate = $request->getData ?? [];

            $filteredDates = [];
            $filteredParts = [];

            foreach ($getDate as $index => $date) {
                if (isset($getPart[$index]) && $getPart[$index] !== '') {
                    $filteredDates[] = $date;
                    $filteredParts[] = $getPart[$index];
                }
            }

            if (count($filteredDates) !== count($filteredParts)) {
                throw new \Exception('予約データ不整合エラー');
            }

            $reserveDays = array_combine($filteredDates, $filteredParts);

            foreach ($reserveDays as $date => $part) {
                $reserve_settings = ReserveSettings::where('setting_reserve', $date)
                    ->where('setting_part', $part)
                    ->first();
                if (!$reserve_settings) {
                    throw new \Exception("予約設定が見つかりません: $date 部 $part");
                }
                $reserve_settings->decrement('limit_users');
                $reserve_settings->users()->attach(Auth::id());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => '予約に失敗しました: ' . $e->getMessage()]);
        }
        return redirect()->route('calendar.general.show', ['user_id' => Auth::id()]);
    }
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $date = $request->reserve_date;
        $partName = $request->reserve_part; // 例: 'リモ1部'

        // 部数を取得(例: '1' を抽出)
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

        // 予約解除（pivotテーブルから削除）
        $reserveSetting->users()->detach($user->id);

        // limit_usersを1増やす（キャンセル分の枠を戻す）
        $reserveSetting->increment('limit_users');

        return redirect()->route('calendar.general.show')->with('status', '予約をキャンセルしました');
    }
}
