<?php

namespace App\Calendars\General;

use App\Models\Calendars\ReserveSettings;
use Carbon\Carbon;
use Auth;

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

    //ユーザー予約を取得
    $userReservations = $user
      ? $user->reserveSettings()->where('setting_reserve', $ymd)->get()
      : collect();
    $reservedParts = $userReservations->pluck('setting_part')->toArray();

    if (count($reservedParts) > 0) {
      //キャンセルボタン表示
      $html = [];
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

    $html[] = '<select name="getPart[]" class="border-primary" style="width:70px; border-radius:5px;" form="reserveParts">';
    $html[] = '<option value="" selected></option>';

    foreach ($parts as $part) {
      $setting = ReserveSettings::where('setting_reserve', $ymd)->where('setting_part', $part)->first();

      if (!$setting) {
        $html[] = '<option value="' . $part . '" disabled>リモ' . $part . '部(受付終了)</option>';
        continue;
      }

      // 予約人数カウント
      $reservedCount = \DB::table('reserve_setting_users')
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
    return Auth::user()->reserveSettings->pluck('setting_reserve')->toArray();
  }

  function authReserveDate($reserveDate)
  {
    return Auth::user()->reserveSettings->where('setting_reserve', $reserveDate);
  }
}
