<?php


namespace Mavericks\Controller\Web\Track;


use Mavericks\Service\Track\MeetService;
use Silex\Application;

class MeetController
{
  /**
   * @var Application
   */
  private $App;

  /**
   * @var MeetService
   */
  private $MeetService;

  public function __construct(Application $App, MeetService $MeetService)
  {
    $this->App         = $App;
    $this->MeetService = $MeetService;
  }

  public function renderCurrentSeasonSchedule()
  {
    return $this->App['twig']->render('Track/schedule.twig', array(
      'meetSchedule' => $this->MeetService->getCurrentSeason()
    ));
  }

  public function renderAthletes()
  {
    return $this->App['twig']->render('Track/athletes.twig', array());
  }

  public function renderMeetResults($meetId)
  {
    return $this->App['twig']->render('Track/meetResults.twig', array(
      'meetDetails' => $this->MeetService->getMeetDetails($meetId),
      'meetResults' => $this->MeetService->getMeetResults($meetId)
    ));
  }

}