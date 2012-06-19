<?php

class Helpers {

  public static function loader($helper) {
    $callback = callback(__CLASS__, $helper);
    if ($callback->isCallable()) {
      return $callback;
    }
  }

  public static function dateHtml5($d) {
    $date = Tools::createDateTime($d);
    return $date->format(DATE_W3C);
  }
  
  public static function datecz($d) {
    $date = Tools::createDateTime($d);
    $format = 'j. n. Y';
		return $date->format($format);
	}
  
  public static function dateTimeCz($d) {
    $date = Tools::createDateTime($d);
    $format = 'j. n. Y - H:i';
		return $date->format($format);
	}
  
  public static function dateCalendar($d) {
    $date = Tools::createDateTime($d);
    $day = $date->format('j');
    $month = $date->format('F');
    $year = $date->format('Y');
    $lang = Environment::getApplication()->getPresenter()->lang;
    $calendar = '<span class="month">'. $month .'</span><span class="day">'. $day .'</span><span class="year">'. $year .'</span>';
    if ($lang == 'cs') {
      $calendar = Utils::czechDay($calendar);
      $calendar = Utils::czechMonth($calendar);
    }
		return $calendar;
	}
  
  public static function dateConcert($d) {
    $date = Tools::createDateTime($d);
    $day = $date->format('j');
    $dayName = $date->format('l');
    $time = $date->format('H:i');
    $month = $date->format('F');
    $year = $date->format('Y');
    $lang = Environment::getApplication()->getPresenter()->lang;
    $calendar = '<span class="name">'. $dayName .'</span><span class="day">'. $day .'</span><span class="time">'. $time .'</span>';
    if ($lang == 'cs') {
      $calendar = Utils::czechDay($calendar);
      $calendar = Utils::czechMonth($calendar);
    }
		return $calendar;
	}
  
  public static function dateConcertHp($d) {
    $date = Tools::createDateTime($d);
    $day = $date->format('j');
    $time = $date->format('H:i');
    $month = $date->format('F');
    $lang = Environment::getApplication()->getPresenter()->lang;
    $calendar = '<span class="month">'. $month .'</span><span class="day">'. $day .'</span><span class="time">'. $time .'</span>';
    if ($lang == 'cs') {
      $calendar = Utils::czechDay($calendar);
      $calendar = Utils::czechMonth($calendar);
    }
		return $calendar;
	}
  
  public static function czechDate($time, $format = 'j.F Y') {
    if (!$time) {
      return FALSE;
    } elseif (is_numeric($time)) {
      $time = (int) $time;
    } elseif ($time instanceof DateTime) {
      $time = $time->format('U');
    } else {
      $time = strtotime($time);
    }
    if ($lang == 'cs') {
      $calendar = Utils::czechDay($calendar);
      $calendar = Utils::czechMonth($calendar);
    }

    return $date;
  }

}