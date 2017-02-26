<?php


namespace Mavericks\Controller\Web\Track;

use Mavericks\Entity\DB\TrackRelayTeam;
use Mavericks\Entity\DB\TrackStudentEvent;
use Mavericks\Entity\ResultMeasurement;
use Mavericks\Entity\ResultTime;
use Maverics\Entity\DB\TrackRelayTeamMember;
use NuevaRunning\Entity\DB\TrackEventResult;
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

  public function getAdminHome()
  {
    return $this->App['twig']->render('Track/Admin/home.twig');
  }


  /**
   * @param $meetId
   * @return mixed
   */
  public function getMeetEventEntry($meetId)
  {
    $data = $this->getMeetPageData($meetId);

    return $this->App['twig']->render('Track/Admin/athleteEventEntry.twig', $data);
  }


  /**
   * @param $meetId
   * @return mixed
   */
  public function getEventEntry($meetId)
  {
    $data = $this->getEventPageData($meetId);

    return $this->App['twig']->render('Track/Admin/eventEntry.twig', $data);
  }

  /**
   * @param $meetId
   * @param $eventId
   * @return mixed
   */
  public function getEditEventEntry($meetId, $eventId)
  {
    $data = $this->getEventEditPageData($meetId, $eventId);

    return $this->App['twig']->render('Track/Admin/editEvent.twig', $data);
  }

  /**
   * @param Request $Request
   * @param $meetId
   * @return mixed
   */
  public function postMeetEventEntry(Request $Request, $meetId)
  {
    $message = '';

    if ($Request->get('formType'))
    {
      $TrackEvent   = $this->createTrackEventFromRequest($Request, $meetId);

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
          $TrackEventResult = $this->createEventResultFromRequest($Request);
          $this->MeetService->addEventResult($TrackEventResult);

          $TrackStudentEvent = $this->createStudentEventFromRequest($Request);
          $this->MeetService->updateStudentEvent($TrackStudentEvent);

          break;
        case 'relayResult':
          $TrackRelayTeam = $this->createRelayTeamFromRequest($Request);
          $this->MeetService->updateRelayTeamResult($TrackRelayTeam);

          break;
      }
    }

    $data            = $this->getMeetPageData($meetId);
    $data['message'] = $message;

    return $this->App['twig']->render('Track/Admin/athleteEventEntry.twig', $data);
  }

  /**
   * @param Request $Request
   * @param $meetId
   * @return mixed
   */
  public function postEventEntry(Request $Request, $meetId)
  {
    $TrackEvent = $this->createTrackEventFromRequest($Request, $meetId);
    $eventId    = $this->MeetService->addMeetEvent($TrackEvent);

    return $this->App->redirect("/track/admin/meet/$meetId/event/$eventId/");
  }

  /**
   * @param Request $Request
   * @param $meetId
   * @param $eventId
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function postEventAthlete(Request $Request, $meetId, $eventId)
  {
    $TrackEvent        = new TrackEvent();
    $TrackEvent->setTrackEventId($eventId);

    foreach ($Request->get('studentId') as $studentId)
    {
      if ($this->MeetService->isRegisteredForEvent($studentId, $eventId))
      {
        continue;
      }

      $TrackStudentEvent = $this->createBasicStudentEvent($meetId, $eventId, $studentId);
      $this->MeetService->addAthleteToEvent($TrackEvent, $TrackStudentEvent);
    }

    return $this->App->redirect("/track/admin/meet/$meetId/event/$eventId/");
  }

  /**
   * @param Request $Request
   * @param $meetId
   * @param $eventId
   * @return mixed
   */
  public function postEventAthleteResult(Request $Request, $meetId, $eventId)
  {
    $TrackEventResult = $this->createEventResultFromRequest($Request);
    $this->MeetService->addEventResult($TrackEventResult);

    $TrackStudentEvent = $this->createStudentEventFromRequest($Request);
    $this->MeetService->updateStudentEvent($TrackStudentEvent);

    return $this->App->redirect("/track/admin/meet/$meetId/event/$eventId/");
  }

  /**
   * @param $meetId
   * @return array
   */
  private function getMeetPageData($meetId)
  {
    $Season = $this->MeetService->getSeasonByMeetId($meetId);

    return  array(
      'meetId'      => $meetId,
      'meetDetails' => $this->MeetService->getMeetDetails($meetId),
      'athletes'    => $this->MeetService->getAthletesBySeason($Season),
      'events'      => $this->MeetService->getEventTypes(),
      'meetResults' => $this->MeetService->getMeetResults($meetId)
    );
  }

  /**
   * @param $meetId
   * @return array
   */
  private function getEventPageData($meetId)
  {
    return  array(
      'meetId'      => $meetId,
      'meetDetails' => $this->MeetService->getMeetDetails($meetId),
      'events'      => $this->MeetService->getEventTypes()
    );
  }

  /**
   * @param $meetId
   * @param $eventId
   * @return array
   */
  private function getEventEditPageData($meetId, $eventId)
  {
    $Season = $this->MeetService->getSeasonByMeetId($meetId);
    $event  = $this->MeetService->getTrackEventDetail($eventId);
    $gender = $event['gender'] === 'Boys' ? 'M' : 'F';
    $athletes = $this->getAthletesForEvent($event['results'], $this->MeetService->getAthletesBySeason($Season, $gender));

    return  array(
      'meetId'      => $meetId,
      'eventId'     => $eventId,
      'meetDetails' => $this->MeetService->getMeetDetails($meetId),
      'event'       => $event,
      'athletes'    => $athletes
    );
  }

  /**
   * @param $eventResults
   * @param $athletes
   * @return mixed
   */
  private function getAthletesForEvent($eventResults, $athletes)
  {
    $eventStudents = array();

    foreach ($eventResults as $student)
    {
      $eventStudents[] = $student['studentId'];
    }

    foreach ($athletes as &$athlete)
    {
      if (in_array($athlete['studentId'], $eventStudents))
      {
        $athlete['checked'] = true;
      }
      else
      {
        $athlete['checked'] = false;
      }
    }

    return $athletes;
  }

  /**
   * @param Request $Request
   * @return TrackEvent
   */
  private function createTrackEventFromRequest(Request $Request, $meetId)
  {
    $TrackEvent = new TrackEvent();
    $TrackEvent
      ->setTrackMeetId($meetId)
      ->setTrackEventTypeId($Request->get('trackEventTypeId'))
      ->setEventGender($Request->get('eventGender'));

    if ($Request->get('eventSubType'))
    {
      $TrackEvent->setEventSubType($Request->get('eventSubType'));
    }

    if ($Request->get('eventStartTime'))
    {
      $eventStartTime = $this->formatStartTime($Request->get('eventStartTime'));
      $Time           = new Time($eventStartTime);

      $TrackEvent->setEventStartTime($Time);
    }

    return $TrackEvent;
  }

  /**
   * @param $time
   * @return string
   */
  private function formatStartTime($time)
  {
    $bits = explode(":", $time);

    $ampm = substr($bits[1], -2);
    $hour = $bits[0];
    $min  = substr($bits[1], 0, 2);

    if ($ampm == 'PM' && $hour < 12)
    {
      $hour += 12;
    }

    return sprintf("%02d:%02d:00", $hour, $min);
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
      ->setStudentId($Request->get('studentId'))
      ->setHasMedaled($Request->get('medaled'));

    if ($Request->get('trackStudentEventId'))
    {
      $TrackStudentEvent->setTrackStudentEventId($Request->get('trackStudentEventId'));
    }

    if ($Request->get('overallPlace'))
    {
      $TrackStudentEvent->setOverallPlace($Request->get('overallPlace'));
    }

    return $TrackStudentEvent;
  }

  /**
   * @param $meetId
   * @param $eventId
   * @param $studentId
   * @return TrackStudentEvent
   */
  public function createBasicStudentEvent($meetId, $eventId, $studentId)
  {
    $TrackStudentEvent = new TrackStudentEvent();
    $TrackStudentEvent
      ->setTrackMeetId($meetId)
      ->setTrackEventId($eventId)
      ->setStudentId($studentId);

    return $TrackStudentEvent;
  }

  /**
   * @param Request $Request
   * @return TrackRelayTeam
   */
  private function createRelayTeamFromRequest(Request $Request)
  {
    $RelayTeam = new TrackRelayTeam();
    $RelayTeam
      ->setRelayTeamName($Request->get('relayTeamName'))
      ->setHasSetSchoolRecord($Request->get('setSchoolRecord'))
      ->setHasMedaled($Request->get('medaled'));

    if ($Request->get('trackRelayTeamId'))
    {
      $RelayTeam->setTrackRelayTeamId($Request->get('trackRelayTeamId'));
    }

    $Result = $this->createResultTimeFromRequest($Request);

    if ($Result)
    {
      $RelayTeam->setResult($Result);
    }

    if ($Request->get('overallPlace'))
    {
      $RelayTeam->setOverallPlace($Request->get('overallPlace'));
    }

    if ($Request->get('place'))
    {
      $RelayTeam->setPlace($Request->get('place'));
    }

    if ($Request->get('heatNumber'))
    {
      $RelayTeam->setHeatNumber((int)$Request->get('heatNumber'));
    }

    return $RelayTeam;
  }

  /**
   * @param Request $Request
   * @return array
   */
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

  /**
   * @param Request $Request
   * @return TrackEventResult
   */
  private function createEventResultFromRequest(Request $Request)
  {
    $TrackEventResult = new TrackEventResult();
    $TrackEventResult
      ->setTrackStudentEventId($Request->get('trackStudentEventId'))
      ->setHasSetSchoolRecord($Request->get('setSchoolRecord'))
      ->setHasSetPersonalRecord($Request->get('setPersonalRecord'));

    if ($Request->get('eventType') === 'track')
    {
      $TrackEventResult->setResultInSeconds($this->createResultTimeFromRequest($Request));

      if ($Request->get('heatNumber'))
      {
        $TrackEventResult->setHeatNumber((int)$Request->get('heatNumber'));
      }
    }
    else
    {
      $TrackEventResult->setResultInInches($this->createResultMeasurementFromRequest($Request));
    }

    if ($Request->get('place'))
    {
      $TrackEventResult->setPlace($Request->get('place'));
    }

    return $TrackEventResult;
  }

  /**
   * @param Request $Request
   * @return ResultTime|null
   */
  private function createResultTimeFromRequest(Request $Request)
  {
    if (!$Request->get('resultMinutes') && !$Request->get('resultSeconds'))
    {
      return null;
    }

    $minutes = $Request->get('resultMinutes') ?: 0;
    $seconds = $Request->get('resultSeconds') + ($minutes * 60);

    return new ResultTime($seconds);
  }

  /**
   * @param Request $Request
   * @return ResultMeasurement|null
   */
  private function createResultMeasurementFromRequest(Request $Request)
  {
    if (!$Request->get('resultFeet') && !$Request->get('resultInches'))
    {
      return null;
    }

    $feet   = $Request->get('resultFeet') ?: 0;
    $inches = $Request->get('resultInches') + ($feet * 12);

    return new ResultMeasurement($inches);
  }
}