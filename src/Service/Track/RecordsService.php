<?php

namespace Mavericks\Service\Track;

use Mavericks\Persistence\TrackSQL;
use Mavericks\Entity\ResultTime;
use Mavericks\Entity\ResultMeasurement;

class RecordsService
{
  const MAX_RECORDS = 5;

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
  public function getSchoolRecords()
  {
    $events = $this->TrackSQL->getEventsWithResults();

    foreach ($events as &$event)
    {
      if ($event['raceType'] =='relay')
      {
        $event['records'] = $this->getSchoolRelayRecords($event);
      }
      else
      {
        $event['records'] = $this->getSchoolIndividualRecords($event);
      }
    }

    return $events;
  }

  public function getAthleteName($studentId)
  {
    $student = $this->TrackSQL->getStudentDataById($studentId);

    if (empty($student))
    {
      return '';
    }

    return $student['firstName'] . ' ' . $student['lastName'];
  }

  public function getAthleteRecords($studentId)
  {
    $events = $this->TrackSQL->getStudentEvents($studentId);

    foreach ($events as &$event)
    {
      $event['records'] = $this->getAthleteEventRecords($studentId, $event['trackEventTypeId'], $event['eventType'], $event['eventGender']);
    }

    return $events;
  }

  /**
   * @param $event
   * @return array|null
   */
  private function getSchoolIndividualRecords($event)
  {
    $records = $this->TrackSQL->getTopEventRecords($event['trackEventTypeId'], $event['eventGender'], self::MAX_RECORDS);

    foreach ($records as &$record)
    {
      $Result           = $event['eventType'] === 'track' ? new ResultTime($record['resultInSeconds']) : new ResultMeasurement($record['resultInInches']);
      $record['result'] = $Result->getResult();
    }

    return $records;
  }

  /**
   * @param $event
   * @return array|null
   */
  private function getSchoolRelayRecords($event)
  {
    $records = $this->TrackSQL->getTopRelayRecords($event['trackEventTypeId'], $event['eventGender'], self::MAX_RECORDS);

    foreach ($records as &$record)
    {
      $Result            = new ResultTime($record['result']);
      $record['result']  = $Result->getResult();
      $record['members'] = $this->TrackSQL->getRelayMembersByTeamId($record['trackRelayTeamId']);
    }

    return $records;
  }

  /**
   * @param $studentId
   * @param $eventTypeId
   * @param $eventType
   * @param $eventGender
   * @return array|null
   */
  private function getAthleteEventRecords($studentId, $eventTypeId, $eventType, $eventGender)
  {

    $schoolRecord = $this->TrackSQL->getTopEventRecords($eventTypeId, $eventGender, 1);
    $records      = $this->TrackSQL->getStudentEventRecords($studentId, $eventTypeId);
    $pr           = null;
    $prKey        = 0;

    foreach ($records as $key => &$record)
    {
      $result                     = $eventType === 'track' ? $record['resultInSeconds'] : $record['resultInInches'];
      $record['isPersonalRecord'] = false;
      $record['isSchoolRecord']   = $this->isSchoolRecord($eventType, $schoolRecord[0], $result);

      if ($this->isNewPersonalRecord($eventType, $pr, $result))
      {
        $pr    = $result;
        $prKey = $key;
      }

      $Result           = $eventType === 'track' ? new ResultTime($record['resultInSeconds']) : new ResultMeasurement($record['resultInInches']);
      $record['result'] = $Result->getResult();
    }

    $records[$prKey]['isPersonalRecord'] = true;

    return $records;
  }

  /**
   * @param string $evenType
   * @param float $personalRecord
   * @param float $value
   * @return bool
   */
  private function isNewPersonalRecord($evenType, $personalRecord, $value)
  {
    if (empty($personalRecord))
    {
      return true;
    }

    if ($evenType === 'track' && $value < $personalRecord)
    {
      return true;
    }

    if ($evenType === 'field' && $value > $personalRecord)
    {
      return true;
    }

    return false;
  }

  /**
   * @param $eventType
   * @param $eventTypeId
   * @param $eventGender
   * @param $record
   * @return bool
   */
  private function isSchoolRecord($eventType, $schoolRecord, $record)
  {
    if ($eventType === 'track')
    {
      return ($record === $schoolRecord['resultInSeconds']);
    }

    return ($record === $schoolRecord['resultInInches']);
  }
}