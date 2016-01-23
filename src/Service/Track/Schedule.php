<?php

namespace NuevaRunning\Service\Track;


use NuevaRunning\Data\CurrentSeason;
use NuevaRunning\Persistence\TrackSQL;

class Schedule
{
  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  private $CurrentSeason;

  public function __construct(TrackSQL $TrackSQL, CurrentSeason $CurrentSeason)
  {
    $this->TrackSQL      = $TrackSQL;
    $this->CurrentSeason = $CurrentSeason;
  }

  /**
   * @return array
   */
  public function getCurrentSeason()
  {
    return $this->TrackSQL->getCurrentSeasonSchedule();
  }

}