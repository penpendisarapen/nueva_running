<?php


namespace Mavericks\Entity\DB;


use Mavericks\Exception\InvalidTimeException;

class Time
{
  /**
   * @var string
   */
  private $time;

  /**
   * Time constructor.
   * @param $time
   */
  public function __construct($time)
  {
    if ($time instanceof Time)
    {
      return $time;
    }

    $this->throwOnInvalidTime($time);

    $this->time = $time;
  }

  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }

  public function __toString()
  {
    return $this->time;
  }

  /**
   * @param $time
   * @throws InvalidTimeException
   */
  private function throwOnInvalidTime($time)
  {
    if (!preg_match('/\d\d:\d\d:\d\d/', $time))
    {
      throw new InvalidTimeException('Invalid time format.');
    }

    list($hour, $minute, $second) = explode(':', $time);

    if ((int)$hour >= 24 || (int)$hour <= 0)
    {
      throw new InvalidTimeException("Hour value is invalid: $hour");
    }

    if ((int)$minute >= 60 || (int)$minute < 0)
    {
      throw new InvalidTimeException("Minute value is invalid: $minute");
    }

    if ((int)$second >= 60 || (int)$second < 0)
    {
      throw new InvalidTimeException("Second value is invalid: $second");
    }
  }
}