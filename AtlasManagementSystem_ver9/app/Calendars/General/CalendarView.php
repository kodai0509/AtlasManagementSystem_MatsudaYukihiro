<?php

namespace App\Calendars\General;

use Carbon\Carbon;
use Auth;

class CalendarView
{
  private $carbon;
  private $userId;

  function __construct($date, $userId)
  {
    $this->carbon = new Carbon($date);
    $this->userId = $userId;
  }

  public function getTitle()
  {
    return $this->carbon->format('Y年n月');
  }

  function render()
  {
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] = '<table class="table">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th>月</th>';
    $html[] = '<th>火</th>';
    $html[] = '<th>水</th>';
    $html[] = '<th>木</th>';
    $html[] = '<th>金</th>';
    $html[] = '<th>土</th>';
    $html[] = '<th>日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';

    $weeks = $this->getWeeks();

    foreach ($weeks as $week) {
      $html[] = '<tr class="' . $week->getClassName() . '">';

      $days = $week->getDays();
      foreach ($days as $day) {
        if (!$day->everyDay()) {
          $html[] = '<td class="calendar-td empty-day" style="background-color: #ddd;"></td>';
          continue;
        }

        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay = $this->carbon->copy()->format("Y-m-d");

        $html[] = ($startDay <= $day->everyDay() && $toDay >= $day->everyDay())
          ? '<td class="calendar-td">'
          : '<td class="calendar-td ' . $day->getClassName() . '">';

        $html[] = $day->render();

        // 予約済み表示（複数部対応）
        $reserves = $day->authReserveDate($day->everyDay());

        if ($reserves->isNotEmpty()) {
          foreach ($reserves as $reserve) {
            $partText = 'リモ' . $reserve->setting_part . '部';

            if ($day->everyDay() < now()->format('Y-m-d')) {
              $html[] = '<p class="m-auto p-0 w-75 text-success" style="font-size:12px;">' . $partText . '</p>';
            } else {
              $html[] = '<button type="button" class="btn btn-danger p-0 w-75 cancel-btn" ' .
                'style="font-size:12px;" ' .
                'data-reserve-date="' . $reserve->setting_reserve . '" ' .
                'data-reserve-part="' . $partText . '">' .
                $partText .
                '</button>';
            }

            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          }
        } else {
          // 未予約時の表示（セレクトボックス or 受付終了）
          if ($day->everyDay() < now()->format('Y-m-d')) {
            $html[] = '<p class="m-auto p-0 w-75 text-secondary" style="font-size:12px;">受付終了</p>';
          } else {
            $html[] = $day->selectPart($day->everyDay());
          }
        }

        $html[] = $day->getDate();
        $html[] = '</td>';
      }

      $html[] = '</tr>';
    }

    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';

    return implode('', $html);
  }

  protected function getWeeks()
  {
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth();
    $lastDay = $this->carbon->copy()->lastOfMonth();
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;

    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();
    while ($tmpDay->lte($lastDay)) {
      $week = new CalendarWeek($tmpDay, count($weeks));
      $weeks[] = $week;
      $tmpDay->addDay(7);
    }
    return $weeks;
  }

  public function selectPart($date)
  {
    $html = [];
    $parts = [1 => '1部', 2 => '2部', 3 => '3部'];

    $html[] = "<select name='reserve_parts[{$date}]' class='border-primary' style='width:100px; border-radius:5px;' form='reserveParts'>";
    $html[] = "<option value='' selected>選択してください</option>";

    foreach ($parts as $part => $label) {
      $reserveSetting = \App\Models\Calendars\ReserveSettings::where('setting_reserve', $date)
        ->where('setting_part', $part)
        ->first();

      if (!$reserveSetting) {
        continue;
      }

      $reservedCount = $reserveSetting->users()->count();
      $remaining = $reserveSetting->limit_users - $reservedCount;

      if ($remaining <= 0) {
        continue;
      }

      $html[] = "<option value='{$part}'>{$label} (残り{$remaining}名)</option>";
    }
    $html[] = "</select>";

    return implode('', $html);
  }
}
