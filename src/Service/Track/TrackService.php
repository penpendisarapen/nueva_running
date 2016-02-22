<?php


namespace Mavericks\Service\Track;


use Mavericks\Persistence\TrackSQL;

class TrackService
{
  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  /**
   * TrackService constructor.
   * @param TrackSQL $TrackSQL
   */
  public function __construct(TrackSQL $TrackSQL)
  {
    $this->TrackSQL = $TrackSQL;
  }

  /**
   * @return array
   */
  public function getLatestAnnouncements()
  {
    return $this->TrackSQL->getCurrentSeasonAnnouncements();
  }

  /**
   * @return array
   */
  public function getUpcomingMeet()
  {
    return $this->TrackSQL->getNextMeet();
  }
}