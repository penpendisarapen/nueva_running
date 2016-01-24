<?php


namespace Mavericks\Controller\Web\Track;


use Mavericks\Service\Track\Schedule;
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

  public function renderCurrentSeasonSchedule()
  {
    return $this->App['twig']->render('Track/schedule.twig', array(
      'meetSchedule' => $this->ScheduleService->getCurrentSeason()
    ));
  }

}