<?php


namespace NuevaRunning\Data;

class CurrentSeason
{
  const SEASON_START_MONTH = 8;

  /**
   * @var int
   */
  private $currentMonth;

  /**
   * @var int
   */
  private $currentYear;

  public function __construct()
  {
    $this->currentMonth = (int)date('n');
    $this->currentYear  = (int)date('Y');
  }

  /**
   * @return string
   */
  public function getStartDate()
  {
    $year = $this->currentYear;

    if ($this->currentMonth < self::SEASON_START_MONTH)
    {
      $year--;
    }

    return sprintf("%d-%02d-01", $year, self::SEASON_START_MONTH);
  }

  public function getEndDate()
  {
    $year = $this->currentYear;

    if ($this->currentMonth >= self::SEASON_START_MONTH)
    {
      $year++;
    }

    return sprintf("%d-%02d-31", $year, self::SEASON_START_MONTH - 1);
  }
}