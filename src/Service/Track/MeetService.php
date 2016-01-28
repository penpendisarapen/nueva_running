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

  public function __construct(TrackSQL $TrackSQL)
  {
    $this->TrackSQL = $TrackSQL;
  }

  /**
   * @return array
   */
  public function getCurrentSeason()
  {
    return $this->TrackSQL->getCurrentSeasonSchedule();
  }

}