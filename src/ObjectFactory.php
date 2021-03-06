<?php


namespace Mavericks;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Mavericks\Data\CurrentSeason;
use Mavericks\Persistence\StudentSQL;
use Mavericks\Persistence\TrackSQL;
use Mavericks\Repository\StudentService;
use Mavericks\Service\Track\MeetService;
use Mavericks\Service\Track\RecordsService;
use Mavericks\Service\Track\TrackService;

class ObjectFactory
{
  private $App;

  /**
   * @param Application $App
   */
  public function __construct(Application $App)
  {
    $this->App = $App;
  }

  public function createTrackService()
  {
    return new TrackService($this->createTrackSQL());
  }
  /**
   * @return MeetService
   */
  public function createMeetService()
  {
    return new MeetService($this->createTrackSQL());
  }

  /**
   * @return RecordsService
   */
  public function createRecordsService()
  {
    return new RecordsService($this->createTrackSQL());
  }

  /**
   * @return StudentService
   */
  public function createStudentService()
  {
    return new StudentService($this->createStudentSQL());
  }

  /**
   * @return StudentSQL
   */
  private function createStudentSQL()
  {
    return new StudentSQL($this->App, $this->createCurrentSeason());
  }
  /**
   * @return TrackSQL
   */
  private function createTrackSQL()
  {
    return new TrackSQL($this->App);
  }

  /**
   * @return CurrentSeason
   */
  public function createCurrentSeason()
  {
    return new CurrentSeason();
  }

}