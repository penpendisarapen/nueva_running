<?php


namespace Mavericks;


use Mavericks\Data\CurrentSeason;
use Mavericks\Persistence\StudentSQL;
use Mavericks\Persistence\TrackSQL;
use Mavericks\Repository\StudentService;
use Mavericks\Service\Track\Schedule;

class ObjectFactory
{
  public function createScheduleService()
  {
    return new Schedule($this->createTrackSQL(), $this->createCurrentSeason());
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
    return new StudentSQL($this->createCurrentSeason());
  }
  /**
   * @return TrackSQL
   */
  private function createTrackSQL()
  {
    return new TrackSQL($this->createCurrentSeason());
  }

  /**
   * @return CurrentSeason
   */
  private function createCurrentSeason()
  {
    return new CurrentSeason();
  }


}