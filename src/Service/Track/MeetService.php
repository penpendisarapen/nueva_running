<?php

namespace Mavericks\Service\Track;


use Mavericks\Data\CurrentSeason;
use Mavericks\Persistence\TrackSQL;

class MeetService
{
  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  /**
   * @var CurrentSeason
   */
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