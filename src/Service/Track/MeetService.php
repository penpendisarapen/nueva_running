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
  public function getAthletesBySeason(Season $Season)
  {
    $data     = $this->TrackSQL->getStudentsBySeason($Season);
    $athletes = array();

    foreach ($data as $student)
    {
      $athletes[] = array(
        'studentId'   => $student['studentId'],
        'studentName' => $student['firstName'] . ' ' . $student['lastName']
      );
    }

    return $athletes;
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
  public function getEventsByMeetId($meetId)
  {
    return $this->TrackSQL->getEventsByMeetId($meetId);
  }

  /**
   * @param $meetId
   * @return array
   */
  public function getMeetResults($meetId)
  {
    $events = $this->TrackSQL->getEventsByMeetId($meetId);

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
      $Result = $eventType === 'track' ? new ResultTime($result['resultInSeconds']) : new ResultMeasurement($result['resultInInches']);

      $result['result'] = $Result->getResult();
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