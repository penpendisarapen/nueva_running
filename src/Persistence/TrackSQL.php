<?php


namespace Mavericks\Persistence;

use Mavericks\Data\CurrentSeason;

class TrackSQL extends SQLPersistence
{

  /**
   * @return array
   */
  public function getCurrentSeasonAnnouncements($limit = 5, $offset = 0)
  {
    $sql = "
      SELECT
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
        announcementDate
      LIMIT $offset, $limit
    ";

    $bind_params = array(
      ':seasonStart' => $this->CurrentSeason->getStartDate(),
      ':seasonEnd'   => $this->CurrentSeason->getEndDate()
    );

    try
    {
      return $this->fetch($sql, $bind_params);
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
        M.meetDate BETWEEN :seasonStart AND :seasonEnd
      ORDER BY
        M.meetDate
    ";

    $bind_params = array(
      ':seasonStart' => $this->CurrentSeason->getStartDate(),
      ':seasonEnd'   => $this->CurrentSeason->getEndDate()
    );

    try
    {
      return $this->fetch($sql, $bind_params);
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
  public function getEventsByMeetId($meetId)
  {
    $sql = "
      SELECT
        E.trackEventId,
        E.eventGender,
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
        E.eventGender DESC,
        T.eventType,
        T.raceType DESC,
        T.distance
    ";

    $bind_params = array(':meetId' => $meetId);

    try
    {
      return $this->fetch($sql, $bind_params);
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
        SE.overallPlace,
        SE.medaled,
        R.heatNumber,
        R.resultInSeconds,
        R.resultInInches,
        R.place,
        R.isFinal
      FROM
        TrackStudentEvent SE
      JOIN
        Student S ON SE.studentId = S.studentId
      JOIN
        TrackEventResult R ON SE.trackStudentEventId = R.trackStudentEventId
      WHERE
        SE.trackEventId = :eventId
      ORDER BY
        R.resultInSeconds,
        R.resultInInches DESC
    ";

    $bind_params = array(':eventId' => $eventId);

    try
    {
      return $this->fetch($sql, $bind_params);
    }
    catch (\PDOException $e)
    {
      error_log($e->getMessage());
      return array('error');
    }
  }

}