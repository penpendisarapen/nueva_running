<?php

namespace Mavericks\Service\Track;

use Mavericks\Persistence\TrackSQL;
use Mavericks\Entity\ResultTime;
use Mavericks\Entity\ResultMeasurement;
use NuevaRunning\Entity\Grade;

class RecordsService
{
  const MAX_RECORDS = 5;

  /**
   * @var TrackSQL
   */
  private $TrackSQL;

  /**
   * RecordsService constructor.
   * @param TrackSQL $TrackSQL
   */
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

        $records          = $this->TrackSQL->getTopEventRecords($event['trackEventTypeId'], $event['eventGender'], self::MAX_RECORDS);
        $event['records'] = $this->formatIndividualRecords($records, $event['eventType']);
      }
    }

    return $events;
  }

  /**
   * @param Grade $Grade
   * @return array
   */
  public function getSchoolRecordsByGrade(Grade $Grade)
  {
    $events           = $this->TrackSQL->getEventsWithResults();
    $individualEvents = array();

    foreach ($events as $event)
    {
      if ($event['raceType'] =='relay')
      {
        continue;
      }

      $records            = $this->TrackSQL->getTopeEventRecordsByGrade($event['trackEventTypeId'], $event['eventGender'], $Grade->getGrade(), self::MAX_RECORDS);

      if (empty($records))
      {
        continue;
      }

      $event['records']   = $this->formatIndividualRecords($records, $event['eventType']);
      $individualEvents[] = $event;
    }

    return $individualEvents;
  }

  /**
   * @param $studentId
   * @return string
   */
  public function getAthleteName($studentId)
  {
    $student = $this->TrackSQL->getStudentDataById($studentId);

    if (empty($student))
    {
      return '';
    }

    return $student['firstName'] . ' ' . $student['lastName'];
  }

  /**
   * @param $studentId
   * @return array|null
   */
  public function getAthleteRecords($studentId)
  {
    $events = $this->TrackSQL->getStudentEvents($studentId);

    if (empty($events))
    {
      return array();
    }

    foreach ($events as &$event)
    {
      $event['records'] = $this->getAthleteEventRecords($studentId, $event['trackEventTypeId'], $event['eventType'], $event['eventGender']);
    }

    return $events;
  }

  /**
   * @param $studentId
   * @return array
   */
  public function getAthleteRelayRecords($studentId)
  {
    $events = $this->TrackSQL->getStudentRelayEvents($studentId);

    if (empty($events))
    {
      return array();
    }

    foreach ($events as &$event)
    {
      $event['records'] = $this->getAthleteRelayEventRecords($studentId, $event['trackEventTypeId'], $event['eventGender']);
    }

    return $events;
  }

  /**
   * @param $records
   * @param $eventType
   * @return mixed
   */
  private function formatIndividualRecords($records, $eventType)
  {
    foreach ($records as &$record)
    {
      $Result           = $eventType === 'track' ? new ResultTime($record['resultInSeconds']) : new ResultMeasurement($record['resultInInches']);
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

    if (!is_array($records))
    {
      return null;
    }

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

    if (!is_array($records))
    {
      return array();
    }

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

      $ResultTime       = $eventType === 'track' ? new ResultTime($record['resultInSeconds']) : new ResultMeasurement($record['resultInInches']);
      $record['result'] = $ResultTime->getResult();
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
   * @param $schoolRecord
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

  /**
   * @param $studentId
   * @param $eventTypeId
   * @param $eventGender
   * @return array|null
   */
  private function getAthleteRelayEventRecords($studentId, $eventTypeId, $eventGender)
  {
    $schoolRecord = $this->TrackSQL->getTopRelayRecords($eventTypeId, $eventGender, 1);
    $records      = $this->TrackSQL->getStudentRelayRecords($studentId, $eventTypeId);

    foreach ($records as $key => &$record)
    {
      $result                   = $record['result'];
      $ResultTime               = new ResultTime($result);
      $record['result']         = $ResultTime->getResult();
      $record['isSchoolRecord'] = ($schoolRecord[0]['result'] === $result);
      $record['members']        = $this->TrackSQL->getRelayMembersByTeamId($record['trackRelayTeamId']);
    }

    return $records;
  }
}