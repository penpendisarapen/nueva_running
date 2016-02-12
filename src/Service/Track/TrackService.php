<?php


namespace Mavericks\Service\Track;


use Mavericks\Persistence\TrackSQL;

class TrackService
{
  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  public function __construct(TrackSQL $TrackSQL)
  {
    $this->TrackSQL = $TrackSQL;
  }

  public function getLatestAnnouncements()
  {
    return $this->TrackSQL->getCurrentSeasonAnnouncements();
  }
}