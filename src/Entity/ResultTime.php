<?php


namespace Mavericks\Entity;


use Mavericks\Exception\InvalidResultTimeException;

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

    $format   = ($this->seconds >= self::SECONDS_IN_HOUR) ? '%1$d:%2$02d:%3$02d.%4$02d' : '%2$d:%3$02d.%4$02d';
    $hours    = gmdate("H", $this->seconds);
    $minutes  = gmdate("i", $this->seconds);
    $seconds  = gmdate("s", $this->seconds);
    $parts    = explode(".", $this->seconds);
    $fraction = isset($parts[1]) ? $parts[1] : '00';

    return sprintf($format, $hours, $minutes, $seconds, $fraction);
  }

  /**
   * @return float
   */
  public function getResultInSeconds()
  {
    return $this->seconds;
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
   * @throws InvalidResultTimeException
   */
  private function throwOnInvalidSeconds($seconds)
  {
    if (!is_float($seconds))
    {
      throw new InvalidResultTimeException("$seconds must be a float value.");
    }
  }

}