<?php


namespace Mavericks\Data;

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
    return sprintf("%d-%02d-01", $this->getStartYear(), self::SEASON_START_MONTH);
  }

  /**
   * @return string
   */
  public function getEndDate()
  {
    return sprintf("%d-%02d-31", $this->getEndYear(), self::SEASON_START_MONTH - 1);
  }

  /**
   * @return int
   */
  public function getStartYear()
  {
    $year = $this->currentYear;

    if ($this->currentMonth < self::SEASON_START_MONTH)
    {
      $year--;
    }

    return $year;
  }

  /**
   * @return int
   */
  public function getEndYear()
  {
    $year = $this->currentYear;

    if ($this->currentMonth >= self::SEASON_START_MONTH)
    {
      $year++;
    }

    return $year;
  }
}