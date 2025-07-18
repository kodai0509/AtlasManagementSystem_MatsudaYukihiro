<?php

namespace App\Calendars\Admin;

use Carbon\Carbon;

class CalendarWeek
{
  protected $carbon;
  protected $index = 0;

  function __construct($date, $index = 0)
  {
    $this->carbon = new Carbon($date);
    $this->index = $index;
  }

  function getClassName()
  {
    return "week-" . $this->index;
  }

  function getDays()
  {
    $days = [];
    $startDay = $this->carbon->copy()->startOfWeek();
    $lastDay = $this->carbon->copy()->endOfWeek();
    $tmpDay = $startDay->copy();

    while ($tmpDay->lte($lastDay)) {
      // 月が異なる日は空白セルとしてCalendarWeekBlankDayクラスを使う
      if ($tmpDay->month != $this->carbon->month) {
        $day = new CalendarWeekBlankDay($tmpDay->copy());
        $days[] = $day;
        $tmpDay->addDay(1);
        continue;
      }
      // 現在の月の日付はCalendarWeekDayクラスで処理
      $day = new CalendarWeekDay($tmpDay->copy());
      $days[] = $day;
      $tmpDay->addDay(1);
    }
    return $days;
  }
}
