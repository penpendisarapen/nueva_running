<?php


namespace NuevaRunning\Controller\API\Track;


use NuevaRunning\Service\Track\Schedule;
use Silex\Application;

class ScheduleController
{
  /**
   * @var Application
   */
  private $App;

  /**
   * @var Schedule
   */
  private $ScheduleService;

  public function __construct(Application $App, Schedule $ScheduleService)
  {
    $this->App             = $App;
    $this->ScheduleService = $ScheduleService;
  }

  public function getCurrentSchedule()
  {
    $data = $this->ScheduleService->getCurrentSeason();
    return $this->App->json($data);
  }

}