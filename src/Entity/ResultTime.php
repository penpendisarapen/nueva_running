<?php


namespace NuevaRunning\Entity;


use Mavericks\Exception\InvalidResultTime;

class ResultTime implements Result
{
  const SECONDS_IN_HOUR = 3600;
  const SECONDS_IN_MINUTE = 60;

  /**
   * @var float
   */
  private $seconds;

  /**
   * ResultTime constructor.
   * @param $seconds
   */
  public function __construct($seconds)
  {
    if ($seconds instanceof ResultTime)
    {
      return $seconds;
    }

    if (!is_float($seconds))
    {
      $seconds = (float)$seconds;
    }

    $this->throwOnInvalidSeconds($seconds);

    $this->seconds = $seconds;
  }

  /**
   * @return string
   */
  public function getResult()
  {
    if ($this->seconds < self::SECONDS_IN_MINUTE)
    {
      return $this->seconds;
    }

    $format   = ($this->seconds >= self::SECONDS_IN_HOUR) ? '%1$s:%2$s:%3$s.%4$02d' : '%2$s:%3$s.%4$02d';
    $hours    = gmdate("H", $this->seconds);
    $minutes  = gmdate("m", $this->seconds);
    $seconds  = gmdate("s", $this->seconds);
    $parts    = explode(".", $this->seconds);
    $fraction = $parts[1];

    return sprintf($format, $hours, $minutes, $seconds, $fraction);
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getResult();
  }

  /**
   * @param $seconds
   * @throws InvalidResultTime
   */
  private function throwOnInvalidSeconds($seconds)
  {
    if (!is_float($seconds))
    {
      throw new InvalidResultTime("$seconds must be a float value.");
    }
  }

}