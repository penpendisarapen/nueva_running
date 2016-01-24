<?php


namespace Mavericks\Persistence;

use Mavericks\Data\CurrentSeason;

class TrackSQL extends SQLPersistence
{

  /**
   * @param CurrentSeason $CurrentSeason
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
        DATE_FORMAT(M.meetDate, '%l%p') AS meetTime,
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


}