<?php


namespace Mavericks\Controller\API\Track;


use Mavericks\Service\Track\MeetService;
use Silex\Application;

class ScheduleController
{
  /**
   * @var Application
   */
  private $App;

  /**
   * @var MeetService
   */
  private $ScheduleService;

  public function __construct(Application $App, MeetService $ScheduleService)
  {
    $this->App             = $App;
    $this->ScheduleService = $ScheduleService;
  }

  public function getCurrentSchedule()
  {
    $data = $this->ScheduleService->getCurrentSeasonSchedule();
    return $this->App->json($data);
  }

}