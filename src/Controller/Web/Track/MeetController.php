<?php


namespace Mavericks\Controller\Web\Track;


use Mavericks\Data\CurrentSeason;
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
   * @param Request $Request
   * @return mixed
   */
  public function renderAthletes(Request $Request)
  {
    $year = $Request->get('season');

    if (!$year)
    {
      $CurrentSeason = new CurrentSeason();
      $year          = $CurrentSeason->getEndYear();
    }

    $Season = new Season($year);

    return $this->App['twig']->render('Track/athletes.twig', array(
      'selectedSeason' => $year,
      'firstSeason'    => $this->MeetService->getFirstSeasonYear(),
      'currentSeason'  => $this->App['service.currentSeason']->getEndYear(),
      'athletes'       => $this->MeetService->getAthletesBySeason($Season)
    ));
  }

  /**
   * @param $meetId
   * @return mixed
   */
  public function renderMeetResults($meetId)
  {
    $meetDetails       = $this->MeetService->getMeetDetails($meetId);
    $showEventSchedule = (strtotime($meetDetails['meetDate']) > strtotime('tomorrow'));

    return $this->App['twig']->render('Track/meetResults.twig', array(
      'showEventSchedule' => $showEventSchedule,
      'meetDetails'       => $meetDetails,
      'meetResults'       => $this->MeetService->getMeetResults($meetId, $showEventSchedule)
    ));
  }

  /**
   * @return mixed
   */
  public function renderResources()
  {
    return $this->App['twig']->render('Track/resources.twig', array());
  }

}