<?php


namespace NuevaRunning\Entity;


use Mavericks\Exception\InvalidResultTime;

class ResultTime implements Result
{
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
    $fraction = 100 * ($this->seconds - (int)$this->seconds);

    return sprintf("%s.%02d", gmdate($this->getTimeFormat(), (int)$this->seconds), $fraction);
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

  private function getTimeFormat()
  {
    if  ($this->seconds >= 3600)
    {
      return "H:i:s";
    }
    elseif ($this->seconds >= 60)
    {
      return "i:s";
    }
    else
    {
      return "s";
    }
  }
}