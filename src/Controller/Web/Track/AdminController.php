<?php


namespace Mavericks\Controller\Web\Track;

use Mavericks\Entity\DB\TrackRelayTeam;
use Mavericks\Entity\DB\TrackStudentEvent;
use Maverics\Entity\DB\TrackRelayTeamMember;
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

    if ($Request->get('formType'))
    {
      $TrackEvent   = $this->createTrackEventFromRequest($Request);

      switch ($Request->get('formType'))
      {
        case 'event':
          $StudentEvent = $this->createStudentEventFromRequest($Request);

          if ($this->MeetService->addAthleteToEvent($TrackEvent, $StudentEvent))
          {
            $message = 'SUCCESS!';
          }

          break;
        case 'relay':
          $RelayTeam   = $this->createRelayTeamFromRequest($Request);
          $TeamMembers = $this->createRelayTeamMembersFromRequest($Request);

          if ($this->MeetService->addRelayTeamEvent($TrackEvent, $RelayTeam, $TeamMembers))
          {
            $message = 'SUCCESS!';
          }

          break;
        case 'result':
          break;
      }
    }

    return $this->App['twig']->render('Track/Admin/athleteEventEntry.twig', array(
      'meetId'        => $meetId,
      'meetDetails'   => $this->MeetService->getMeetDetails($meetId),
      'athletes'      => $this->MeetService->getAthletesBySeason($Season),
      'events'        => $this->MeetService->getEventTypes(),
      'meetResults' => $this->MeetService->getMeetResults($meetId),
      'message'       => $message
    ));
  }

  /**
   * @param Request $Request
   * @return TrackEvent
   */
  private function createTrackEventFromRequest(Request $Request)
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
  private function createStudentEventFromRequest(Request $Request)
  {
    $TrackStudentEvent = new TrackStudentEvent();
    $TrackStudentEvent
      ->setTrackMeetId($Request->get('trackMeetId'))
      ->setStudentId($Request->get('studentId'));

    return $TrackStudentEvent;
  }

  private function createRelayTeamFromRequest(Request $Request)
  {
    $RelayTeam = new TrackRelayTeam();
    $RelayTeam->setRelayTeamName($Request->get('relayTeamName'));

    return $RelayTeam;
  }

  private function createRelayTeamMembersFromRequest(Request $Request)
  {
    $TeamMembers = array();

    foreach ($Request->get('studentId') as $studentId)
    {
      $TeamMember = new TrackRelayTeamMember();
      $TeamMember->setStudentId($studentId);

      $TeamMembers[] = $TeamMember;
    }
    return $TeamMembers;
  }
}