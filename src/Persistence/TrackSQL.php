<?php


namespace Mavericks\Persistence;

use Mavericks\Entity\DB\Time;
use Mavericks\Entity\DB\TrackEvent;
use Mavericks\Entity\DB\TrackRelayTeam;
use Mavericks\Entity\DB\TrackStudentEvent;
use Mavericks\Entity\ResultMeasurement;
use Mavericks\Entity\ResultTime;
use Mavericks\Entity\Season;
use Maverics\Entity\DB\TrackRelayTeamMember;
use NuevaRunning\Entity\DB\TrackEventResult;

class TrackSQL extends SQLPersistence
{
  /**
   * @param int $limit
   * @param int $offset
   * @return array
   */
  public function getCurrentSeasonAnnouncements($limit = 5, $offset = 0)
  {
    $sql = "
      SELECT
        subject,
        content,
        DATE_FORMAT(announcementDate, '%b %e, %Y') AS `date`,
        author
      FROM
        Announcement
      WHERE
        sport = 'track'
      AND
        announcementDate BETWEEN :seasonStart AND :seasonEnd
      ORDER BY
        announcementDate DESC
      LIMIT $offset, $limit
    ";

    $bindParams = array(
      ':seasonStart' => $this->CurrentSeason->getStartDate(),
      ':seasonEnd'   => $this->CurrentSeason->getEndDate()
    );

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @return array
   */
  public function getCurrentSeasonSchedule()
  {
    $Season = new Season($this->CurrentSeason->getEndYear());
    return $this->getSeasonSchedule($Season);
  }

  /**
   * @param Season $Season
   * @return array
   */
  public function getSeasonSchedule(Season $Season)
  {
    $sql = "
      SELECT
        D.trackMeetDetailId,
        D.meetName,
        D.meetType,
        M.trackMeetId,
        DATE_FORMAT(M.meetDate, '%b %e') AS meetDate,
        DATE_FORMAT(M.meetDate, '%l:%i %p') AS meetTime,
        M.teamRequired,
        M.isOptional,
        M.resultsURL,
        M.meetSubName,
        L.locationId,
        L.locName,
        L.locStreet1,
        L.locStreet2,
        L.locCity,
        L.locState,
        L.locZipCode
      FROM
        TrackMeet M
      JOIN
        TrackMeetDetails D ON D.trackMeetDetailId = M.trackMeetDetailId
      LEFT JOIN
        Location L ON M.locationId = L.locationId
      WHERE
        YEAR(meetDate) = :season
      ORDER BY
        M.meetDate
    ";

    $bindParams = array(
      ':season' => $Season
    );

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @return array
   */
  public function getNextMeet()
  {
    $sql = "
      SELECT
        D.trackMeetDetailId,
        D.meetName,
        D.meetType,
        M.trackMeetId,
        DATE_FORMAT(M.meetDate, '%b %e, %Y') AS meetDate,
        DATE_FORMAT(M.meetDate, '%l:%i %p') AS meetTime,
        M.teamRequired,
        M.isOptional,
        M.resultsURL,
        M.meetSubName,
        L.locationId,
        L.locName,
        L.locStreet1,
        L.locStreet2,
        L.locCity,
        L.locState,
        L.locZipCode
      FROM
        TrackMeet M
      JOIN
        TrackMeetDetails D ON D.trackMeetDetailId = M.trackMeetDetailId
      LEFT JOIN
        Location L ON M.locationId = L.locationId
      WHERE
        meetDate >= NOW()
      ORDER BY
        M.meetDate
      LIMIT 1
    ";

    try
    {
      $result = $this->fetch($sql);
      return $result[0];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $meetId
   * @return array
   */
  public function getWeatherByMeetId($meetId)
  {
    $sql = "
      SELECT
        trackMeetId,
        high,
        low,
        conditions,
        windAverage,
        iconUrl
      FROM
        TrackWeatherForecast
      WHERE
        trackMeetId = :meetId
    ";

    $bindParams = array(
      ':meetId' => $meetId
    );

    try
    {
      $result = $this->fetch($sql, $bindParams);

      if (empty($result))
      {
        return array();
      }

      return $result[0];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }

  }

  /**
   * @param Season $Season
   * @return array
   */
  public function getStudentsBySeason(Season $Season)
  {
    $sql = "
      SELECT
        S.studentId,
        S.firstName,
        S.lastName,
        S.gender,
        TSS.grade
      FROM
        TrackStudentSeason TSS
      JOIN
        Student S ON TSS.studentId = S.studentId
      WHERE
        TSS.season = :season
      ORDER BY
        S.firstName,
        S.lastName
    ";

    $bindParams = array(':season' => $Season);

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $meetId
   * @return int|null
   */
  public function getSeasonByMeetId($meetId)
  {
    $sql = "
      SELECT
        DATE_FORMAT(meetDate, '%Y') AS season
      FROM
        TrackMeet
      WHERE
        trackMeetId = :meetId
    ";

    $bindParams = array(':meetId' => $meetId);

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results[0]['season'];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $meetId
   * @return array
   */
  public function getMeetDetailsById($meetId)
  {
    $sql = "
      SELECT
        D.meetName,
        DATE_FORMAT(M.meetDate, '%b %e, %Y') AS meetDate,
        M.resultsURL,
        M.meetSubName,
        L.locName,
        L.locCity,
        L.locState
      FROM
        TrackMeet M
      JOIN
        TrackMeetDetails D ON D.trackMeetDetailId = M.trackMeetDetailId
      LEFT JOIN
        Location L ON M.locationId = L.locationId
      WHERE
        M.trackMeetId = :meetId
    ";

    $bindParams = array(':meetId' => $meetId);

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return array();
      }

      return $results[0];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $meetId
   * @param bool $sortByStartTime
   * @return array
   */
  public function getEventsByMeetId($meetId, $sortByStartTime = false)
  {
    $startTimeSort = $sortByStartTime ? 'E.eventStartTime,' : '';

    $sql = "
      SELECT
        E.trackEventId,
        E.eventGender,
        E.eventSubType,
        DATE_FORMAT(E.eventStartTime, '%l:%i %p') AS eventStartTime,
        T.trackEventTypeId,
        T.eventName,
        T.eventType,
        T.raceType
      FROM
        TrackEvent E
      JOIN
        TrackEventType T ON E.trackEventTypeId = T.trackEventTypeId
      WHERE
        E.trackMeetId = :meetId
      ORDER BY
        $startTimeSort
        E.eventGender DESC,
        T.eventType,
        T.raceType DESC,
        T.distance
    ";

    $bindParams = array(':meetId' => $meetId);

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $eventId
   * @return array
   */
  public function getResultsByEventId($eventId)
  {
    $sql = "
      SELECT
        S.studentId,
        S.firstName,
        S.lastName,
        SE.trackStudentEventId,
        SE.overallPlace,
        SE.medaled,
        R.heatNumber,
        R.resultInSeconds,
        R.resultInInches,
        R.place,
        R.isFinal,
        R.setSchoolRecord,
        R.setPersonalRecord
      FROM
        TrackStudentEvent SE
      JOIN
        Student S ON SE.studentId = S.studentId
      LEFT JOIN
        TrackEventResult R ON SE.trackStudentEventId = R.trackStudentEventId
      WHERE
        SE.trackEventId = :eventId
      ORDER BY
        R.resultInSeconds,
        R.resultInInches DESC
    ";

    $bindParams = array(':eventId' => $eventId);

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $eventId
   * @return array
   */
  public function getRelayResultsByEventId($eventId)
  {
    $sql = "
      SELECT
        trackRelayTeamId,
        relayTeamName,
        result,
        place,
        medaled,
        heatNumber,
        overallPlace,
        setSchoolRecord
      FROM
        TrackRelayTeam
      WHERE
        trackEventId = :eventId
    ";

    $bindParams = array(':eventId' => $eventId);

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $teamId
   * @return array
   */
  public function getRelayMembersByTeamId($teamId)
  {
    $sql = "
      SELECT
        S.firstName,
        S.lastName,
        M.studentId,
        M.individualDistance,
        M.splitTime
      FROM
        TrackRelayTeamMember M
      JOIN
        Student S ON M.studentId = S.studentId
      WHERE
        trackRelayTeamId = :teamId;
    ";

    $bindParams = array(':teamId' => $teamId);

    try
    {
      return $this->fetch($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @return array
   */
  public function getEventTypes()
  {
    $sql = "
      SELECT
        trackEventTypeId,
        eventName,
        eventType,
        raceType
      FROM
        TrackEventType
      ORDER BY
        raceType,
        eventType,
        eventName
    ";

    try
    {
      return $this->fetch($sql);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $eventTypeId
   * @return array
   */
  public function getEventTypeById($eventTypeId)
  {
    $sql = "
      SELECT
        trackEventTypeId,
        eventName,
        eventType,
        raceType
      FROM
        TrackEventType
      WHERE
        trackEventTypeId = :eventTypeId
    ";

    $bindParams = array(':eventTypeId' => $eventTypeId);

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results[0];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @return array
   */
  public function getEventsWithResults()
  {
    $sql = "
      SELECT DISTINCT
        TET.trackEventTypeId,
        TE.eventGender,
        TET.eventName,
        TET.eventType,
        TET.raceType
      FROM
        TrackEventType TET
      JOIN
        TrackEvent TE ON TET.trackEventTypeId = TE.trackEventTypeId
      ORDER BY
        TET.raceType,
        TET.eventType,
        TET.distance,
        TE.eventGender DESC
    ";

    try
    {
      return $this->fetch($sql);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $eventTypeId
   * @param $gender
   * @param int $limit
   * @return array|null
   */
  public function getTopEventRecords($eventTypeId, $gender, $limit = 1)
  {
    $sql = "
      SELECT
        S.firstName,
        S.lastName,
        TER.resultInSeconds,
        TER.resultInInches,
        TMD.meetName,
        TM.meetSubName,
        DATE_FORMAT(TM.meetDate, '%b %e, %Y') AS meetDate
      FROM
        TrackEvent TE
      JOIN
        TrackMeet TM ON TE.trackMeetId = TM.trackMeetId
      JOIN
        TrackMeetDetails TMD ON TM.trackMeetDetailId = TMD.trackMeetDetailId
      JOIN
        TrackStudentEvent TSE ON TE.trackEventId = TSE.trackEventId
      JOIN
        TrackEventResult TER ON TSE.trackStudentEventId = TER.trackStudentEventId
      JOIN
        Student S ON TSE.studentId = S.studentId
      WHERE
        TE.trackEventTypeID = :eventTypeId
      AND
        TE.eventGender = :gender
      ORDER BY
        TER.resultInSeconds,
        TER.resultInInches DESC,
        TM.meetDate
      LIMIT $limit;
    ";

    $bindParams = array(
      ':gender'      => $gender,
      ':eventTypeId' => $eventTypeId
    );

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $eventTypeId
   * @param $gender
   * @param $grade
   * @param int $limit
   * @return array|null
   */
  public function getTopeEventRecordsByGrade($eventTypeId, $gender, $grade, $limit = 1)
  {
    $sql = "
      SELECT
        S.firstName,
        S.lastName,
        TER.resultInSeconds,
        TER.resultInInches,
        TMD.meetName,
        TM.meetSubName,
        DATE_FORMAT(TM.meetDate, '%b %e, %Y') AS meetDate
      FROM
        TrackEvent TE
      JOIN
        TrackMeet TM ON TE.trackMeetId = TM.trackMeetId
      JOIN
        TrackMeetDetails TMD ON TM.trackMeetDetailId = TMD.trackMeetDetailId
      JOIN
        TrackStudentEvent TSE ON TE.trackEventId = TSE.trackEventId
      JOIN
        TrackEventResult TER ON TSE.trackStudentEventId = TER.trackStudentEventId
      JOIN
        Student S ON TSE.studentId = S.studentId
      JOIN
        TrackStudentSeason TSS ON TSE.studentId = TSS.studentId AND TSS.grade = :grade AND YEAR(TM.meetDate) = TSS.season
      WHERE
        TE.trackEventTypeID = :eventTypeId
      AND
        TE.eventGender = :gender
      ORDER BY
        TER.resultInSeconds,
        TER.resultInInches DESC,
        TM.meetDate
      LIMIT $limit;
    ";

    $bindParams = array(
      ':gender'      => $gender,
      ':eventTypeId' => $eventTypeId,
      ':grade'       => $grade
    );

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $eventTypeId
   * @param $gender
   * @param int $limit
   * @return array|null
   */
  public function getTopRelayRecords($eventTypeId, $gender, $limit = 1)
  {
    $sql = "
      SELECT
        TRT.trackRelayTeamId,
        TRT.result,
        TMD.meetName,
        TM.meetSubName,
        DATE_FORMAT(TM.meetDate, '%b %e, %Y') AS meetDate
      FROM
        TrackEvent TE
      JOIN
        TrackMeet TM ON TE.trackMeetId = TM.trackMeetId
      JOIN
        TrackMeetDetails TMD ON TM.trackMeetDetailId = TMD.trackMeetDetailId
      JOIN
        TrackRelayTeam TRT ON TE.trackEventId = TRT.trackEventId AND TRT.result > 0
      WHERE
        TE.trackEventTypeId = :eventTypeId
      AND
        TE.eventGender = :gender
      ORDER BY
        TRT.result
      LIMIT $limit
    ";

    $bindParams = array(
      ':gender'      => $gender,
      ':eventTypeId' => $eventTypeId
    );

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  public function getStudentDataById($studentId)
  {
    $sql = "
      SELECT
        firstName,
        lastName,
        gender,
        class
      FROM
        Student
      WHERE
        studentId = :studentId
    ";

    $bindParams = array(':studentId' => $studentId);

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results[0];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $studentId
   * @return array|null
   */
  public function getStudentEvents($studentId)
  {
    $sql = "
      SELECT DISTINCT
        TET.trackEventTypeId,
        TET.eventName,
        TET.eventType,
        TET.raceType,
        TE.eventGender
      FROM
        TrackEventType TET
      JOIN
        TrackEvent TE ON TET.trackEventTypeId = TE.trackEventTypeId
      JOIN
        TrackStudentEvent TSE ON TE.trackEventId = TSE.trackEventId
      AND
        TSE.studentId = :studentId
      ORDER BY
        TET.raceType,
        TET.eventType,
        TET.distance,
        TE.eventGender DESC
    ";

    $bindParams = array(':studentId' => $studentId);

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $studentId
   * @param $eventId
   * @return array|bool
   */
  public function isRegisteredForEvent($studentId, $eventId)
  {
    $sql = "
      SELECT 1 FROM TrackStudentEvent WHERE studentId = :studentId AND trackEventId = :eventId
    ";

    $bindParams = array(
      ':studentId' => $studentId,
      ':eventId'   => $eventId
    );

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return false;
      }

      return true;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $studentId
   * @param $eventTypeId
   * @return array|null
   */
  public function getStudentEventRecords($studentId, $eventTypeId)
  {
    $sql = "
      SELECT
        TER.resultInSeconds,
        TER.resultInInches,
        TMD.meetName,
        TM.meetSubName,
        DATE_FORMAT(TM.meetDate, '%b %e, %Y') AS meetDate,
        TER.place,
        TSE.overallPlace,
        TSE.medaled
      FROM
        TrackEvent TE
      JOIN
        TrackMeet TM ON TE.trackMeetId = TM.trackMeetId
      JOIN
        TrackMeetDetails TMD ON TM.trackMeetDetailId = TMD.trackMeetDetailId
      JOIN
        TrackStudentEvent TSE ON TE.trackEventId = TSE.trackEventId AND TSE.studentId = :studentId
      JOIN
        TrackEventResult TER ON TSE.trackStudentEventId = TER.trackStudentEventId
      WHERE
        TE.trackEventTypeID = :eventTypeId
      ORDER BY
        TM.meetDate;
    ";

    $bindParams = array(
      ':studentId'   => $studentId,
      ':eventTypeId' => $eventTypeId
    );

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $studentId
   * @return array|null
   */
  public function getStudentRelayEvents($studentId)
  {
    $sql = "
      SELECT DISTINCT
        TET.trackEventTypeId,
        TET.eventName,
        TET.eventType,
        TET.raceType,
        TE.eventGender
      FROM
        TrackEventType TET
      JOIN
        TrackEvent TE ON TET.trackEventTypeId = TE.trackEventTypeId
      JOIN
        TrackRelayTeam TRT ON TE.trackEventId = TRT.trackEventId
      JOIN
        TrackRelayTeamMember TRTM ON TRT.trackRelayTeamId = TRTM.trackRelayTeamId AND TRTM.studentId = :studentId
    ";

    $bindParams = array(':studentId' => $studentId);

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  /**
   * @param $studentId
   * @param $eventTypeId
   * @return array|null
   */
  public function getStudentRelayRecords($studentId, $eventTypeId)
  {
    $sql = "
      SELECT
        TRT.trackRelayTeamId,
        TRT.result,
        TRT.overallPlace,
        TRT.medaled,
        TMD.meetName,
        TM.meetSubName,
        DATE_FORMAT(TM.meetDate, '%b %e, %Y') AS meetDate
      FROM
        TrackEvent TE
      JOIN
        TrackMeet TM ON TE.trackMeetId = TM.trackMeetId
      JOIN
        TrackMeetDetails TMD ON TM.trackMeetDetailId = TMD.trackMeetDetailId
      JOIN
        TrackRelayTeam TRT ON TE.trackEventId = TRT.trackEventId
      JOIN
        TrackRelayTeamMember TRTM ON TRT.trackRelayTeamId = TRTM.trackRelayTeamId AND TRTM.studentId = :studentId
      WHERE
        TE.trackEventTypeId = :eventTypeId
      ORDER BY
        TM.meetDate
    ";

    $bindParams = array(
      ':studentId'   => $studentId,
      ':eventTypeId' => $eventTypeId
    );

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return null;
      }

      return $results;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

  public function getPersonalRecord($eventTypeId, $studentId)
  {
  }

  /**
   * @param TrackEvent $TrackEvent
   * @return int
   */
  public function getMeetEventId(TrackEvent $TrackEvent)
  {
    $sql = "
      SELECT
        trackEventId
      FROM
        TrackEvent
      WHERE
        trackMeetId = :trackMeetId
      AND
        trackEventTypeId = :trackEventTypeId
      AND
        eventGender = :eventGender
    ";

    $bindParams = array(
      ':trackMeetId'      => $TrackEvent->getTrackMeetId(),
      ':trackEventTypeId' => $TrackEvent->getTrackEventTypeId(),
      ':eventGender'      => $TrackEvent->getEventGender(),
    );

    if ($TrackEvent->getEventSubType())
    {
      $sql .= 'AND eventSubType = :eventSubType';
      $bindParams[':eventSubType'] = $TrackEvent->getEventSubType();
    }

    try
    {
      $results = $this->fetch($sql, $bindParams);

      if (empty($results))
      {
        return 0;
      }

      return $results[0]['trackEventId'];
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param $trackEventId
   * @return TrackEvent|null
   */
  public function getTrackEventById($trackEventId)
  {
    $sql = "
      SELECT
        trackEventId,
        trackMeetId,
        trackEventTypeId,
        eventGender,
        eventSubType,
        eventStartTime
      FROM
        TrackEvent
      WHERE
        trackEventId = :trackEventId
    ";

    $bindParams = array(':trackEventId' => $trackEventId);

    try
    {
      $results    = $this->fetch($sql, $bindParams);
      $TrackEvent = new TrackEvent();

      if (empty($results))
      {
        return $TrackEvent;
      }

      $TrackEvent
        ->setTrackEventId($results[0]['trackEventId'])
        ->setTrackMeetId($results[0]['trackMeetId'])
        ->setTrackEventTypeId($results[0]['trackEventTypeId'])
        ->setEventGender($results[0]['eventGender'])
        ->setEventSubType($results[0]['eventSubType']);

      if ($results[0]['eventStartTime'])
      {
        $TrackEvent->setEventStartTime(new Time($results[0]['eventStartTime']));
      }

      return $TrackEvent;
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return null;
    }
  }

  /**
   * @param TrackEvent $TrackEvent
   * @return int
   */
  public function addTrackEvent(TrackEvent $TrackEvent)
  {
    $fields = array(
      'trackMeetId',
      'trackEventTypeId',
      'eventGender',
      'eventSubType'
    );

    $values = array(
      ':trackMeetId',
      ':trackEventTypeId',
      ':eventGender',
      ':eventSubType'
    );

    $bindParams = array(
      ':trackMeetId'      => $TrackEvent->getTrackMeetId(),
      ':trackEventTypeId' => $TrackEvent->getTrackEventTypeId(),
      ':eventGender'      => $TrackEvent->getEventGender(),
      ':eventSubType'     => $TrackEvent->getEventSubType()
    );

    if ($TrackEvent->getEventStartTime())
    {
      $fields[]                      = 'eventStartTime';
      $values[]                      = ':eventStartTime';
      $bindParams[':eventStartTime'] = $TrackEvent->getEventStartTime();
    }

    $sql = 'INSERT INTO TrackEvent (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

    try
    {
      return $this->insert($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackEvent $TrackEvent
   * @return int
   */
  public function updateTrackEvent(TrackEvent $TrackEvent)
  {
    $sql = "
      UPDATE
        TrackEvent
      SET
        trackMeetId      = :trackMeetId,
        trackEventTypeId = :trackEventTypeId,
        eventGender      = :eventGender,
        eventSubType     = :eventSubType,
        eventStartTime   = :eventStartTime
      WHERE
        trackEventId = :trackEventId
    ";

    $bindParams = array(
      ':trackEventId'     => $TrackEvent->getTrackEventId(),
      ':trackMeetId'      => $TrackEvent->getTrackMeetId(),
      ':trackEventTypeId' => $TrackEvent->getTrackEventTypeId(),
      ':eventGender'      => $TrackEvent->getEventGender(),
      ':eventSubType'     => $TrackEvent->getEventSubType(),
      ':eventStartTime'   => $TrackEvent->setEventStartTime()
    );

    try
    {
      return $this->update($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackStudentEvent $TrackStudentEvent
   * @return int|string
   */
  public function addStudentEvent(TrackStudentEvent $TrackStudentEvent)
  {
    $fields = array(
      'trackMeetId',
      'trackEventId',
      'studentId'
    );

    $values = array(
      ':trackMeetId',
      ':trackEventId',
      ':studentId'
    );

    $bindParams = array(
      ':trackMeetId'  => $TrackStudentEvent->getTrackMeetId(),
      ':trackEventId' => $TrackStudentEvent->getTrackEventId(),
      ':studentId'    => $TrackStudentEvent->getStudentId()
    );

    $sql = 'INSERT INTO TrackStudentEvent (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

    try
    {
      return $this->insert($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackStudentEvent $TrackStudentEvent
   * @return int
   */
  public function updateStudentEvent(TrackStudentEvent $TrackStudentEvent)
  {
    $sql = "
      UPDATE
        TrackStudentEvent
      SET
        medaled = :medaled
    ";

    $bindParams = array(
      ':medaled'             => $TrackStudentEvent->hasMedaled() ? 1 : 0,
      ':trackStudentEventId' => $TrackStudentEvent->getTrackStudentEventId()
    );

    if ($TrackStudentEvent->getOverallPlace())
    {
      $sql .= ', overallPlace = :overallPlace';
      $bindParams[':overallPlace'] = $TrackStudentEvent->getOverallPlace();
    }

    $sql .= "
      WHERE
        trackStudentEventId = :trackStudentEventId
    ";

    try
    {
      return $this->update($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackEventResult $TrackEventResult
   * @return int|string
   */
  public function addEventResult(TrackEventResult $TrackEventResult)
  {
    $fields = array(
      'trackStudentEventId',
      'setSchoolRecord',
      'setPersonalRecord'
    );

    $values = array(
      ':trackStudentEventId',
      ':setSchoolRecord',
      ':setPersonalRecord'
    );

    $bindParams = array(
      ':trackStudentEventId' => $TrackEventResult->getTrackStudentEventId(),
      ':setSchoolRecord'     => $TrackEventResult->hasSetSchoolRecord() ? 1 : 0,
      ':setPersonalRecord'   => $TrackEventResult->hasSetPersonalRecord() ? 1 : 0
    );

    if ($TrackEventResult->getResultInSeconds() instanceof ResultTime)
    {
      $fields[]                       = 'resultInSeconds';
      $values[]                       = ':resultInSeconds';
      $bindParams[':resultInSeconds'] = $TrackEventResult->getResultInSeconds()->getResultInSeconds();
    }

    if ($TrackEventResult->getResultInInches() instanceof ResultMeasurement)
    {
      $fields[]                      = 'resultInInches';
      $values[]                      = ':resultInInches';
      $bindParams[':resultInInches'] = $TrackEventResult->getResultInInches()->getResultInInches();
    }

    if ($TrackEventResult->getHeatNumber())
    {
      $fields[]                  = 'heatNumber';
      $values[]                  = ':heatNumber';
      $bindParams[':heatNumber'] = $TrackEventResult->getHeatNumber();
    }

    if ($TrackEventResult->getPlace())
    {
      $fields[]             = 'place';
      $values[]             = ':place';
      $bindParams[':place'] = $TrackEventResult->getPlace();
    }

    $sql = 'INSERT INTO TrackEventResult (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

    try
    {
      return $this->insert($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackRelayTeam $TrackRelayTeam
   * @return int|string
   */
  public function addRelayTeam(TrackRelayTeam $TrackRelayTeam)
  {
    $fields = array(
      'trackEventId',
      'relayTeamName'
    );

    $values = array(
      ':trackEventId',
      ':relayTeamName'
    );

    $bindParams = array(
      ':trackEventId'  => $TrackRelayTeam->getTrackEventId(),
      ':relayTeamName' => $TrackRelayTeam->getRelayTeamName()
    );

    $sql = 'INSERT INTO TrackRelayTeam (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

    try
    {
      return $this->insert($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackRelayTeam $TrackRelayTeam
   * @return int
   */
  public function updateRelayTeamResults(TrackRelayTeam $TrackRelayTeam)
  {
    $sql = "
      UPDATE
        TrackRelayTeam
      SET
    ";

    $bindParams = array(
      ':trackRelayTeamId' => $TrackRelayTeam->getTrackRelayTeamId()
    );

    if ($TrackRelayTeam->getResult() instanceof ResultTime)
    {
      $fields[]              = 'result = :result';
      $bindParams[':result'] = $TrackRelayTeam->getResult()->getResultInSeconds();
    }

    if ($TrackRelayTeam->getPlace())
    {
      $fields[]             = 'place = :place';
      $bindParams[':place'] = $TrackRelayTeam->getPlace();
    }

    if ($TrackRelayTeam->hasMedaled())
    {
      $fields[]               = 'medaled = :medaled';
      $bindParams[':medaled'] = $TrackRelayTeam->hasMedaled() ? 1 : 0;
    }

    if ($TrackRelayTeam->getHeatNumber())
    {
      $fields[]                  = 'heatNumber = :heatNumber';
      $bindParams[':heatNumber'] = $TrackRelayTeam->getHeatNumber();
    }

    if ($TrackRelayTeam->getOverallPlace())
    {
      $fields[]                    = 'overallPlace = :overallPlace';
      $bindParams[':overallPlace'] = $TrackRelayTeam->getOverallPlace();
    }

    if ($TrackRelayTeam->hasSetSchoolRecord())
    {
      $fields[]                       = 'setSchoolRecord = :setSchoolRecord';
      $bindParams[':setSchoolRecord'] = $TrackRelayTeam->hasSetSchoolRecord() ? 1 : 0;
    }

    if (empty($fields))
    {
      return 0;
    }

    $sql .= implode(', ', $fields);
    $sql .= "
      WHERE
        trackRelayTeamId = :trackRelayTeamId
    ";

    try
    {
      return $this->update($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }

  /**
   * @param TrackRelayTeamMember $TeamMember
   * @return int|\PDOStatement
   */
  public function addRelayTeamMember(TrackRelayTeamMember $TeamMember)
  {
    $fields = array(
      'trackRelayTeamId',
      'studentId'
    );

    $values = array(
      ':trackRelayTeamId',
      ':studentId'
    );

    $bindParams = array(
      ':trackRelayTeamId' => $TeamMember->getTrackRelayTeamId(),
      ':studentId'        => $TeamMember->getStudentId()
    );

    $sql = 'INSERT INTO TrackRelayTeamMember (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

    try
    {
      return $this->query($sql, $bindParams);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return 0;
    }
  }
}