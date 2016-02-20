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

  /**
   * @param $event
   * @return array|null
   */
  private function getSchoolIndividualRecords($event)
  {
    $records = $this->TrackSQL->getTopEventRecords($event['trackEventTypeId'], $event['eventGender'], self::MAX_RECORDS);

    foreach ($records as &$record)
    {
      $Result             = $event['eventType'] === 'track' ? new ResultTime($record['resultInSeconds']) : new ResultMeasurement($record['resultInInches']);
      $record['result']   = $Result->getResult();
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
      $Result = new ResultTime($record['result']);
      $record['result']   = $Result->getResult();
      $record['members'] = $this->TrackSQL->getRelayMembersByTeamId($record['trackRelayTeamId']);
    }

    return $records;
  }

}