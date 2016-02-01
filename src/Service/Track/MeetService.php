<?php

namespace Mavericks\Service\Track;

use Mavericks\Persistence\TrackSQL;
use NuevaRunning\Entity\ResultMeasurement;
use NuevaRunning\Entity\ResultTime;

class MeetService
{
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
    $events      = $this->TrackSQL->getEventsByMeetId($meetId);
    $meetResults = array();

    foreach ($events as $event)
    {
      $event['eventGender'] = $event['eventGender'] === 'G' ? 'Girls' : 'Boys';
      $event['results']     = $this->getEventResults($event['trackEventId'], $event['eventType']);
      $meetResults[]        = $event;
    }

    return $meetResults;
  }

  /**
   * @param $eventId
   * @param $eventType
   * @return array
   */
  private function getEventResults($eventId, $eventType)
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

}