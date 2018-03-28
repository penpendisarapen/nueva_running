<?php

namespace Mavericks\Service\Track;

use Mavericks\Entity\DB\TrackEvent;
use Mavericks\Entity\DB\TrackRelayTeam;
use Mavericks\Entity\DB\TrackStudentEvent;
use Mavericks\Entity\Season;
use Mavericks\Persistence\TrackSQL;
use Mavericks\Entity\ResultMeasurement;
use Mavericks\Entity\ResultTime;
use Maverics\Entity\DB\TrackRelayTeamMember;
use NuevaRunning\Entity\DB\TrackEventResult;
use Symfony\Component\HttpFoundation\Request;


class MeetService
{
  const EVENT_TYPE_TRACK = 'track';
  const EVENT_TYPE_FIELD = 'field';

  const RACE_TYPE_SOLO = 'individual';
  const RACE_TYPE_RELAY = 'relay';

  const FIRST_SEASON = 2014;

  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  public function __construct(TrackSQL $TrackSQL)
  {
    $this->TrackSQL = $TrackSQL;
  }

  /**
   * @return int
   */
  public function getFirstSeasonYear()
  {
    return self::FIRST_SEASON;
  }

  /**
   * @return array
   */
  public function getCurrentSeasonSchedule()
  {
    return $this->TrackSQL->getCurrentSeasonSchedule();
  }

  /**
   * @param Season $Season
   * @return array
   */
  public function getSeasonSchedule(Season $Season)
  {
    return $this->TrackSQL->getSeasonSchedule($Season);
  }

  /**
   * @return array
   */
  public function getMeetList()
  {
    return $this->TrackSQL->getTrackMeetList();
  }

  public function getLocationList()
  {
    $locations = $this->TrackSQL->getLocationList();
    $locationById = [];

    foreach ($locations as $location)
    {
      $display = sprintf('%s, %s, %s, %s', $location['locName'], $location['locStreet1'], $location['locCity'], $location['locState']);
      $locationById[$location['locationId']] = $display;
    }

    return $locationById;
  }

  public function addMeetToSchedule(Request $Request)
  {
    $trackMeetDetailId = $this->getTrackMeetDetailId($Request);
    $locationId        = $this->getLocationId($Request);

    if (!$trackMeetDetailId || !$locationId)
    {
      return 0;
    }

    list($month, $day, $year) = explode('/', $Request->get('meetDate'));

    $meetData = [
      'trackMeetDetailId' => $trackMeetDetailId,
      'locationId'        => $locationId,
      'meetDate'          => sprintf('%s-%s-%s %s:00', $year, $month, $day, $Request->get('meetTime')),
      'teamRequired'      => $Request->get('teamRequired'),
      'isOptional'        => intval($Request->get('isOptional')),
      'meetSubName'       => $Request->get('meetSubName')
    ];

    return $this->TrackSQL->addTrackMeet($meetData);
  }

  /**
   * @param Request $Request
   * @return int
   */
  private function getTrackMeetDetailId(Request $Request)
  {
    if ($Request->get('trackMeetDetailId'))
    {
      return $Request->get('trackMeetDetailId');
    }

    $meetDetails = [
      'meetName' => $Request->get('meetName'),
      'meetType' => $Request->get('meetType')
    ];

    return $this->TrackSQL->addTrackMeetDetails($meetDetails);
  }

  /**
   * @param Request $Request
   * @return int
   */
  private function getLocationId(Request $Request)
  {
    if ($Request->get('locationId'))
    {
      return $Request->get('locationId');
    }

    $location = [
      'locName'    => $Request->get('locName'),
      'locStreet1' => $Request->get('locStreet1'),
      'locStreet2' => '',
      'locCity'    => $Request->get('locCity'),
      'locState'   => $Request->get('locState'),
      'locZipCode' => $Request->get('locZipCode')
    ];

    return $this->TrackSQL->addLocation($location);
  }

  /**
   * @param $meetId
   * @return Season
   */
  public function getSeasonByMeetId($meetId)
  {
    $season = $this->TrackSQL->getSeasonByMeetId($meetId);
    return new Season($season);
  }

  /**
   * @param Season $Season
   * @return array
   */
  public function getAthletesBySeason(Season $Season, $gender = null)
  {
    $data = $this->TrackSQL->getStudentsBySeason($Season);
    $athletes = array();

    foreach ($data as $athlete)
    {
      if ($gender && $athlete['gender'] !== $gender)
      {
        continue;
      }

      $athlete['studentName'] = $athlete['firstName'] . ' ' . $athlete['lastName'];
      $athletes[] = $athlete;
    }

    return $athletes;
  }

  /**
   * @param Season $Season
   * @return array
   */
  public function getAthletesNotOnTeam(Season $Season)
  {
    $data = $this->TrackSQL->getCurrentStudentsNotOnTeam($Season);
    $athletes = [];

    foreach ($data as $athlete)
    {
      $athlete['grade']       = $this->getGradeFromGraduationYear($Season, $athlete['class']);
      $athlete['studentName'] = $athlete['firstName'] . ' ' . $athlete['lastName'];
      $athletes[]             = $athlete;
    }

    return $athletes;
  }

  /**
   * @param Season $Season
   * @param array $studentData
   */
  public function addAthletesToTeam(Season $Season, array $studentData)
  {
    $this->TrackSQL->addStudentsToTrackSeason($Season, $studentData);
  }

  /**
   * @param Season $Season
   * @param array $studentData
   */
  public function addNewAthleteToTeam(Season $Season, array $studentData)
  {
    $studentData['class'] = $this->getGraduatingClassFromGrade($Season, $studentData['grade']);
    $studentId = $this->TrackSQL->addNewStudent($studentData);

    if ($studentId)
    {
      $studentData['studentId'] = $studentId;
      $this->TrackSQL->addStudentsToTrackSeason($Season, [$studentData]);
    }
  }

  /**
   * @param Season $Season
   * @param int $year
   * @return int
   */
  private function getGradeFromGraduationYear(Season $Season, $year)
  {
    $grades = [
      0 => 12,
      1 => 11,
      2 => 10,
      3 => 9
    ];

    $diff = $year - intval($Season->getSeason());

    return $grades[$diff];
  }

  /**
   * @param Season $Season
   * @param $grade
   * @return int
   */
  private function getGraduatingClassFromGrade(Season $Season, $grade)
  {
    $yearDiff = [
      9  => 3,
      10 => 2,
      11 => 1,
      12 => 0
    ];

    return intval($Season->getSeason() + $yearDiff[$grade]);
  }

  /**
   * @param $meetId
   * @return array
   */
  public function getMeetDetails($meetId)
  {
    return $this->TrackSQL->getMeetDetailsById($meetId);
  }

  /**
   * @param $meetId
   * @return array
   */
  public function getWeatherForecastByMeetId($meetId)
  {
    return $this->TrackSQL->getWeatherByMeetId($meetId);
  }

  /**
   * @param $meetId
   * @return array
   */
  public function getEventsByMeetId($meetId)
  {
    return $this->TrackSQL->getEventsByMeetId($meetId);
  }

  /**
   * @param $meetId
   * @param bool $sortByStartTime
   * @return array
   */
  public function getMeetResults($meetId, $sortByStartTime = false)
  {
    $events = $this->TrackSQL->getEventsByMeetId($meetId, $sortByStartTime);

    $meetResults = array(
      'individual' => array(),
      'relay'      => array()
    );

    if (empty($events))
    {
      return $meetResults;
    }

    foreach ($events as $event)
    {
      $event['results']                  = $this->getEventResults($event['trackEventId'], $event['eventType'], $event['raceType']);
      $meetResults[$event['raceType']][] = $event;
    }

    return $meetResults;
  }

  /**
   * @return array
   */
  public function getEventTypes()
  {
    return $this->TrackSQL->getEventTypes();
  }

  /**
   * @param $eventId
   * @return array
   */
  public function getTrackEventDetail($eventId)
  {
    $TrackEvent     = $this->TrackSQL->getTrackEventById($eventId);
    $trackEventType = $this->TrackSQL->getEventTypeById($TrackEvent->getTrackEventTypeId());

    return array(
      'name'      => $trackEventType['eventName'],
      'type'      => $trackEventType['eventType'],
      'gender'    => $TrackEvent->getEventGender(),
      'subType'   => $TrackEvent->getEventSubType(),
      'startTime' => $TrackEvent->getEventStartTime(),
      'results'   => $this->getEventResults($eventId, $trackEventType['eventType'], $trackEventType['raceType'])
    );
  }

  /**
   * @param $studentId
   * @param $eventId
   * @return array|bool
   */
  public function isRegisteredForEvent($studentId, $eventId)
  {
    return $this->TrackSQL->isRegisteredForEvent($studentId, $eventId);
  }

  /**
   * @param TrackEvent $TrackEvent
   * @param TrackStudentEvent $TrackStudentEvent
   * @return int|string
   */
  public function addAthleteToEvent(TrackEvent $TrackEvent, TrackStudentEvent $TrackStudentEvent)
  {
    if (!$TrackEvent->getTrackEventId())
    {
      $TrackStudentEvent->setTrackEventId($this->getMeetEventId($TrackEvent));
    }

    if (!$TrackStudentEvent->getTrackEventId())
    {
      return 0;
    }

    return $this->TrackSQL->addStudentEvent($TrackStudentEvent);
  }

  /**
   * @param TrackStudentEvent $TrackStudentEvent
   * @return int
   */
  public function updateStudentEvent(TrackStudentEvent $TrackStudentEvent)
  {
    return $this->TrackSQL->updateStudentEvent($TrackStudentEvent);
  }

  /**
   * @param $trackStudentEventId
   * @return int
   */
  public function deleteStudentEvent($trackStudentEventId)
  {
    return $this->TrackSQL->deleteStudentEvent($trackStudentEventId);
  }

  /**
   * @param TrackEventResult $TrackEventResult
   * @return int|string
   */
  public function addEventResult(TrackEventResult $TrackEventResult)
  {
    return $this->TrackSQL->addEventResult($TrackEventResult);
  }

  /**
   * @param TrackEvent $TrackEvent
   * @return int
   */
  public function getMeetEventId(TrackEvent $TrackEvent)
  {
    $eventId = $this->TrackSQL->getMeetEventId($TrackEvent);

    if ($eventId)
    {
      return $eventId;
    }

    return $this->TrackSQL->addTrackEvent($TrackEvent);
  }

  /**
   * @param TrackEvent $TrackEvent
   * @return int
   */
  public function addMeetEvent(TrackEvent $TrackEvent)
  {
    return $this->TrackSQL->addTrackEvent($TrackEvent);
  }

  /**
   * @param $trackEventId
   * @return int|string
   */
  public function deleteTrackEvent($trackEventId)
  {
    return $this->TrackSQL->deleteTrackEvent($trackEventId);
  }

  /**
   * @param TrackEvent $TrackEvent
   * @param TrackRelayTeam $RelayTeam
   * @param $TeamMembers TrackRelayTeamMember[]
   * @return int
   */
  public function addRelayTeamEvent(TrackEvent $TrackEvent, TrackRelayTeam $RelayTeam, $TeamMembers)
  {
    if (!$TrackEvent->getTrackEventId())
    {
      $RelayTeam->setTrackEventId($this->getMeetEventId($TrackEvent));
    }

    if (!$RelayTeam->getTrackEventId())
    {
      return 0;
    }

    $trackRelayTeamId = $this->TrackSQL->addRelayTeam($RelayTeam);

    if (!$trackRelayTeamId)
    {
      return 0;
    }

    foreach ($TeamMembers as $TeamMember)
    {
      $TeamMember->setTrackRelayTeamId($trackRelayTeamId);
      $this->TrackSQL->addRelayTeamMember($TeamMember);
    }

    return 1;
  }

  /**
   * @param TrackRelayTeam $TrackRelayTeam
   * @return int
   */
  public function updateRelayTeamResult(TrackRelayTeam $TrackRelayTeam)
  {
    return $this->TrackSQL->updateRelayTeamResults($TrackRelayTeam);
  }

  /**
   * @param $eventId
   * @param $eventType
   * @param $raceType
   * @return array
   */
  private function getEventResults($eventId, $eventType, $raceType)
  {
    if ($raceType == self::RACE_TYPE_RELAY)
    {
      return $this->getRelayResults($eventId);
    }

    return $this->getIndvidualResults($eventId, $eventType);
  }

  /**
   * @param $eventId
   * @param $eventType
   * @return array
   */
  private function getIndvidualResults($eventId, $eventType)
  {
    $results = $this->TrackSQL->getResultsByEventId($eventId);

    foreach ($results as &$result)
    {
      $Result                 = $eventType === 'track' ? new ResultTime($result['resultInSeconds']) : new ResultMeasurement($result['resultInInches']);
      $result['result']       = $Result->getResult();
      $result['overallPlace'] = $result['overallPlace'] ?: 'n/a';
    }

    return $results;
  }

  /**
   * @param $eventId
   * @return array
   */
  private function getRelayResults($eventId)
  {
    $results = $this->TrackSQL->getRelayResultsByEventId($eventId);
    $relayResults = array();

    foreach ($results as $result)
    {
      $Result                 = new ResultTime($result['result']);
      $result['result']       = $Result->getResult();
      $result['overallPlace'] = $result['overallPlace'] ?: 'n/a';
      $result['members']      = $this->TrackSQL->getRelayMembersByTeamId($result['trackRelayTeamId']);
      $relayResults[]         = $result;
    }

    return $relayResults;
  }

}