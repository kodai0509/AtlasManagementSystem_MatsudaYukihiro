<?php

namespace App\Calendars\General;

use Carbon\Carbon;
use Auth;

class CalendarView
{

  private $carbon;
  function __construct($date)
  {
    $this->carbon = new Carbon($date);
    $this->userId = Auth::id();
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
          // 日付のないセル
          $html[] = '<td class="calendar-td empty-day" style="background-color: #ddd;"></td>';
          continue;
        }

        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay = $this->carbon->copy()->format("Y-m-d");
        $html[] = ($startDay <= $day->everyDay() && $toDay >= $day->everyDay())
          ? '<td class="calendar-td">'
          : '<td class="calendar-td ' . $day->getClassName() . '">';

        $html[] = $day->render();

        if (in_array($day->everyDay(), $day->authReserveDay())) {
          $reserve = $day->authReserveDate($day->everyDay())->first();
          $parts = [1 => "リモ1部", 2 => "リモ2部", 3 => "リモ3部"];
          $reservePart = $parts[$reserve->setting_part] ?? '';

          if ($day->everyDay() < now()->format('Y-m-d')) {
            $html[] = '<p class="m-auto p-0 w-75 text-success" style="font-size:12px;">' . $reservePart . '</p>';
          } else {
            $html[] = '<button type="submit" class="btn btn-danger p-0 w-75" name="delete_date" style="font-size:12px" value="' . $reserve->setting_reserve . '">' . $reservePart . '</button>';
          }

          $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
        } else {
          // 未参加
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
    $html[] = '<form action="/reserve/calendar" method="post" id="reserveParts">' . csrf_field() . '</form>';
    $html[] = '<form action="/delete/calendar" method="post" id="deleteParts">' . csrf_field() . '</form>';

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

    foreach ($parts as $part => $label) {
      $reservedCount = \App\Models\Calendars\Reserve::where('reserve_date', $date)
        ->where('reserve_part', $part)
        ->count();

      $remaining = 20 - $reservedCount;

      if ($remaining <= 0) {
        $html[] = "<p class='text-danger m-0' style='font-size:12px;'>$label: 満席</p>";
      } else {
        $html[] = "<label style='font-size:12px;'>
                <input type='radio' name='reserve_parts[$date]' value='$part' form='reserveParts'>
                $label（残り: $remaining名）
            </label>";
      }
    }

    return implode('', $html);
  }
}
