<?php

namespace App\Calendars\General;

use App\Models\Calendars\ReserveSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarWeekDay
{
  protected $carbon;

  function __construct($date)
  {
    $this->carbon = new Carbon($date);
  }

  function getClassName()
  {
    return "day-" . strtolower($this->carbon->format("D"));
  }

  function pastClassName()
  {
    return;
  }

  /**
   * @return
   */
  function render()
  {
    return '<p class="day">' . $this->carbon->format("j") . '日</p>';
  }

  function selectPart($ymd)
  {
    $user = Auth::user();

    // 該当日のReserveSettingsを取得してログ出力
    $settings = ReserveSettings::where('setting_reserve', $ymd)->get();
    Log::debug('selectPart: ReserveSettings for ' . $ymd, $settings->toArray());

    //ユーザー予約を取得
    $userReservations = $user
      ? $user->reserveSettings()->where('setting_reserve', $ymd)->get()
      : collect();
    $reservedParts = $userReservations->pluck('setting_part')->toArray();

    if (count($reservedParts) > 0) {
      $html = [];

      $html[] = '<div class="reserved-parts text-success">';
      foreach ($reservedParts as $part) {
        $html[] = '<p class="m-0">予約済：リモ' . e($part) . '部</p>';
      }
      $html[] = '</div>';

      //キャンセルボタン
      foreach ($reservedParts as $part) {
        $partName = 'リモ' . $part . '部';
        $html[] = '<button type="button" class="btn btn-danger cancel-btn" data-toggle="modal" data-target="#cancelModal" ';
        $html[] = 'data-reserve-date="' . e($ymd) . '" ';
        $html[] = 'data-reserve-part="' . e($partName) . '">';
        $html[] = 'キャンセル (' . e($partName) . ')</button>';
      }

      return implode('', $html);
    }

    //残り枠を計算
    $html = [];
    $parts = [1, 2, 3];
    $hasAvailable = false;

    $html[] = '<select name="reserve_parts[' . $ymd . ']" class="border-primary" style="width:70px; border-radius:5px;" form="reserveParts">';
    $html[] = '<option value="" selected></option>';

    foreach ($parts as $part) {
      $setting = ReserveSettings::where('setting_reserve', $ymd)->where('setting_part', $part)->first();

      if (!$setting) {
        $html[] = '<option value="' . $part . '" disabled>リモ' . $part . '部(受付終了)</option>';
        continue;
      }

      // 予約人数カウント
      $reservedCount = DB::table('reserve_setting_users')
        ->where('reserve_setting_id', $setting->id)
        ->count();

      $remaining = $setting->limit_users - $reservedCount;

      if ($remaining > 0) {
        $hasAvailable = true;
        $html[] = '<option value="' . $part . '">リモ' . $part . '部(残り' . $remaining . '枠)</option>';
      } else {
        $html[] = '<option value="' . $part . '" disabled>リモ' . $part . '部(受付終了)</option>';
      }
    }
    $html[] = '</select>';

    if (!$hasAvailable) {
      return '<p>受付終了</p>';
    }

    return implode('', $html);
  }

  function getDate()
  {
    return '<input type="hidden" value="' . $this->carbon->format('Y-m-d') . '" name="getData[]" form="reserveParts">';
  }

  function everyDay()
  {
    return $this->carbon->format('Y-m-d');
  }

  function authReserveDay()
  {
    $user = Auth::user();
    if (!$user) return [];

    $dates = ReserveSettings::whereHas('users', function ($q) use ($user) {
      $q->where('user_id', $user->id);
    })->pluck('setting_reserve')->toArray();

    logger()->debug('authReserveDay dates:', ['dates' => $dates]);

    return $dates;
  }

  function authReserveDate($reserveDate)
  {
    $user = Auth::user();
    if (!$user) return collect();

    return ReserveSettings::where('setting_reserve', $reserveDate)
      ->whereHas('users', function ($q) use ($user) {
        $q->where('user_id', $user->id);
      })
      ->get();
  }
}
