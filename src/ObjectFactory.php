<?php


namespace NuevaRunning;


use NuevaRunning\Data\CurrentSeason;
use NuevaRunning\Persistence\StudentSQL;
use NuevaRunning\Persistence\TrackSQL;
use NuevaRunning\Repository\StudentService;
use NuevaRunning\Service\Track\Schedule;

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