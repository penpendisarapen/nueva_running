<?php


namespace NuevaRunning\Entity;


use NuevaRunning\Exception\InvalidSeasonException;

class Season
{
  const MINIUM_SEASON = 2013;

  /**
   * @var int
   */
  private $season;

  public function __construct($season)
  {
    if ($season instanceof Season)
    {
      return $season;
    }

    if (!is_int($season) && ctype_digit($season))
    {
      $season = (int)$season;
    }

    $this->throwOnInvalidSeason($season);

    $this->season = $season;
  }

  /**
   * @return int
   */
  public function getSeason()
  {
    return $this->season;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string)$this->season;
  }

  /**
   * @param $season
   * @throws InvalidSeasonException
   */
  private function throwOnInvalidSeason($season)
  {
    if (!is_int($season))
    {
      throw new InvalidSeasonException("$season must be an integer.");
    }

    if ($season < self::MINIUM_SEASON)
    {
      throw new InvalidSeasonException("$season must be greater than " . self::MINIUM_SEASON . ".");
    }
  }

}