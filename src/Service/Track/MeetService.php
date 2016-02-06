<?php

namespace Mavericks\Service\Track;

use Mavericks\Entity\Season;
use Mavericks\Persistence\TrackSQL;
use NuevaRunning\Entity\ResultMeasurement;
use NuevaRunning\Entity\ResultTime;

class MeetService
{
  const EVENT_TYPE_TRACK = 'track';
  const EVENT_TYPE_FIELD = 'field';

  const RACE_TYPE_SOLO = 'individual';
  const RACE_TYPE_RELAY = 'relay';

  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  public function __construct(TrackSQL $TrackSQL)
  {
    $this->TrackSQL = $TrackSQL;
  }

  /**
   * @return array
   */
  public function getCurrentSeason()
  {
    return $this->TrackSQL->getCurrentSeasonSchedule();
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
  public function getAthletesBySeasonForAutocomplete(Season $Season)
  {
    $data     = $this->TrackSQL->getStudentsBySeason($Season);
    $athletes = array();

    foreach ($data as $student)
    {
      $athletes[] = array(
        'value'   => $student['studentId'],
        'label' => $student['firstName'] . ' ' . $student['lastName']
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
  public function getMeetResults($meetId)
  {
    $events = $this->TrackSQL->getEventsByMeetId($meetId);

    if (empty($events))
    {
      return array();
    }

    $meetResults = array(
      'individual' => array(),
      'relay'      => array()
    );

    foreach ($events as $event)
    {
      $event['eventGender']              = $this->translateGender($event['eventGender']);
      $event['results']                  = $this->getEventResults($event['trackEventId'], $event['eventType'], $event['raceType']);
      $meetResults[$event['raceType']][] = $event;
    }

    return $meetResults;
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

  /**
   * @param $gender
   * @return string
   */
  private function translateGender($gender)
  {
    switch ($gender)
    {
      case 'G':
        return "Girls";
      case 'B':
        return "Boys";
      case 'M':
        return "Mixed";
    }
  }
}