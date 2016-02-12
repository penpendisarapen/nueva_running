<?php


namespace NuevaRunning\Controller\Web\Track;

use Mavericks\Entity\DB\TrackStudentEvent;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Mavericks\Service\Track\MeetService;
use Mavericks\Entity\DB\TrackEvent;
use Mavericks\Entity\DB\Time;

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

  /**
   * @param Request $Request
   * @param $meetId
   * @return mixed
   */
  public function renderAthleteEventEntry(Request $Request, $meetId)
  {
    $Season = $this->MeetService->getSeasonByMeetId($meetId);
    $message = '';

    if ($Request->get('addAthleteEvent'))
    {
      $TrackEvent   = $this->createTrackEventFromRequest($Request);
      $StudentEvent = $this->createStudentEventFromRequest($Request);

      if ($this->MeetService->addAthleteToEvent($TrackEvent, $StudentEvent))
      {
        $message = 'WIN!';
      }
    }

    return $this->App['twig']->render('Track/Admin/athleteEventEntry.twig', array(
      'meetId'        => $meetId,
      'meetDetails'   => $this->MeetService->getMeetDetails($meetId),
      'athletes'      => $this->MeetService->getAthletesBySeason($Season),
      'events'        => $this->MeetService->getEventTypes(),
      'eventAthletes' => $this->MeetService->getMeetResults($meetId),
      'message'       => $message
    ));
  }

  /**
   * @param Request $Request
   * @return TrackEvent
   */
  public function createTrackEventFromRequest(Request $Request)
  {
    $TrackEvent = new TrackEvent();
    $TrackEvent
      ->setTrackMeetId($Request->get('trackMeetId'))
      ->setTrackEventTypeId($Request->get('trackEventTypeId'))
      ->setEventGender($Request->get('eventGender'));

    if ($Request->get('eventSubType'))
    {
      $TrackEvent->setEventSubType($Request->get('eventSubType'));
    }

    if ($Request->get('eventStartTime'))
    {
      $Time = new Time($Request->get('eventStartTime'));

      $TrackEvent->setEventStartTime($Time);
    }

    return $TrackEvent;
  }

  /**
   * @param Request $Request
   * @return TrackStudentEvent
   */
  public function createStudentEventFromRequest(Request $Request)
  {
    $TrackStudentEvent = new TrackStudentEvent();
    $TrackStudentEvent
      ->setTrackMeetId($Request->get('trackMeetId'))
      ->setStudentId($Request->get('studentId'));

    return $TrackStudentEvent;
  }
}