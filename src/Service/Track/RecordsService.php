<?php

namespace Mavericks\Service\Track;


use Mavericks\Data\CurrentSeason;
use Mavericks\Persistence\TrackSQL;

class RecordsService
{
  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  public function __construct(TrackSQL $TrackSQL)
  {
    $this->TrackSQL = $TrackSQL;
  }
}