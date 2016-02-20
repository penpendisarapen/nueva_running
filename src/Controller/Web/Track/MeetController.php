<?php


namespace Mavericks\Controller\Web\Track;


use Mavericks\Entity\Season;
use Mavericks\Service\Track\MeetService;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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

  /**
   * MeetController constructor.
   * @param Application $App
   * @param MeetService $MeetService
   */
  public function __construct(Application $App, MeetService $MeetService)
  {
    $this->App         = $App;
    $this->MeetService = $MeetService;
  }

  /**
   * @param Request $Request
   * @return mixed
   */
  public function renderCurrentSeasonSchedule(Request $Request)
  {
    if ($Request->get('season'))
    {
      return $this->renderSeasonSchedule($Request->get('season'));
    }

    return $this->App['twig']->render('Track/schedule.twig', array(
      'selectedSeason' => $this->App['service.currentSeason']->getEndYear(),
      'firstSeason'    => $this->MeetService->getFirstSeasonYear(),
      'currentSeason'  => $this->App['service.currentSeason']->getEndYear(),
      'meetSchedule'   => $this->MeetService->getCurrentSeasonSchedule()
    ));
  }

  /**
   * @param $season
   * @return mixed
   */
  public function renderSeasonSchedule($season)
  {
    $Season = new Season($season);

    return $this->App['twig']->render('Track/schedule.twig', array(
      'selectedSeason' => $season,
      'firstSeason'    => $this->MeetService->getFirstSeasonYear(),
      'currentSeason'  => $this->App['service.currentSeason']->getEndYear(),
      'meetSchedule'   => $this->MeetService->getSeasonSchedule($Season)
    ));
  }

  /**
   * @return mixed
   */
  public function renderAthletes()
  {
    return $this->App['twig']->render('Track/athletes.twig', array());
  }

  /**
   * @param $meetId
   * @return mixed
   */
  public function renderMeetResults($meetId)
  {
    return $this->App['twig']->render('Track/meetResults.twig', array(
      'meetDetails' => $this->MeetService->getMeetDetails($meetId),
      'meetResults' => $this->MeetService->getMeetResults($meetId)
    ));
  }

}