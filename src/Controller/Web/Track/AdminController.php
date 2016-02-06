<?php


namespace NuevaRunning\Controller\Web\Track;

use Mavericks\Service\Track\MeetService;
use Silex\Application;

class AdminController
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

  public function renderAthleteEventEntry($meetId)
  {
    $Season   = $this->MeetService->getSeasonByMeetId($meetId);
    $athletes = json_encode($this->MeetService->getAthletesBySeasonForAutocomplete($Season));

    return $this->App['twig']->render('Track/Admin/athleteEventEntry.twig', array(
      'meetId'   => $meetId,
      'athletes' => $athletes
    ));
  }
}