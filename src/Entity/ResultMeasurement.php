<?php


namespace NuevaRunning\Entity;


use Mavericks\Exception\InvalidResultMeasurement;

class ResultMeasurement implements Result
{
  private $inches;

  public function __construct($inches)
  {
    if ($inches instanceof ResultMeasurement)
    {
      return $inches;
    }

    if (!is_float($inches))
    {
      $inches = (float)$inches;
    }

    $this->throwOnInvalidMeasurment($inches);

    $this->inches = $inches;
  }

  /**
   * @return string
   */
  public function getResult()
  {
    $feet   = (int)$this->inches / 12;
    $inches = ($this->inches % 12) + $this->inches - (int)$this->inches;

    return sprintf("%d' %0.2f\"", $feet, $inches);
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getResult();
  }

  private function throwOnInvalidMeasurment($inches)
  {
    if (!is_float($inches))
    {
      throw new InvalidResultMeasurement("$inches must be a float value.");
    }
  }
}