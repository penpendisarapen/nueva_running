<?php

namespace Mavericks\Entity\DB;


class TrackRelayTeam
{
  /**
   * @var int
   */
  private $trackRelayTeamId;

  /**
   * @var int
   */
  private $trackEventId;

  /**
   * @var string
   */
  private $relayTeamName;

  /**
   * @var float
   */
  private $result;

  /**
   * @var int
   */
  private $place;

  /**
   * @var bool
   */
  private $medaled = false;

  /**
   * @var string
   */
  private $heatNumber;

  /**
   * @var int
   */
  private $overallPlace;

  /**
   * @var bool
   */
  private $setSchoolRecord = false;

  /**
   * @return int
   */
  public function getTrackRelayTeamId()
  {
    return $this->trackRelayTeamId;
  }

  /**
   * @param int $trackRelayTeamId
   * @return TrackRelayTeam
   */
  public function setTrackRelayTeamId($trackRelayTeamId)
  {
    $this->trackRelayTeamId = $trackRelayTeamId;

    return $this;
  }

  /**
   * @return int
   */
  public function getTrackEventId()
  {
    return $this->trackEventId;
  }

  /**
   * @param int $trackEventId
   * @return TrackRelayTeam
   */
  public function setTrackEventId($trackEventId)
  {
    $this->trackEventId = $trackEventId;
    return $this;
  }

  /**
   * @return string
   */
  public function getRelayTeamName()
  {
    return $this->relayTeamName;
  }

  /**
   * @param string $relayTeamName
   * @return TrackRelayTeam
   */
  public function setRelayTeamName($relayTeamName)
  {
    $this->relayTeamName = $relayTeamName;
    return $this;
  }

  /**
   * @return float
   */
  public function getResult()
  {
    return $this->result;
  }

  /**
   * @param float $result
   * @return TrackRelayTeam
   */
  public function setResult($result)
  {
    $this->result = $result;
    return $this;
  }

  /**
   * @return int
   */
  public function getPlace()
  {
    return $this->place;
  }

  /**
   * @param int $place
   * @return TrackRelayTeam
   */
  public function setPlace($place)
  {
    $this->place = $place;
    return $this;
  }

  /**
   * @return bool
   */
  public function medaled()
  {
    return $this->medaled;
  }

  /**
   * @param bool $medaled
   * @return TrackRelayTeam
   */
  public function setMedaled($medaled)
  {
    $this->medaled = $medaled;
    return $this;
  }

  /**
   * @return string
   */
  public function getHeatNumber()
  {
    return $this->heatNumber;
  }

  /**
   * @param string $heatNumber
   * @return TrackRelayTeam
   */
  public function setHeatNumber($heatNumber)
  {
    $this->heatNumber = $heatNumber;
    return $this;
  }

  /**
   * @return int
   */
  public function getOverallPlace()
  {
    return $this->overallPlace;
  }

  /**
   * @param int $overallPlace
   * @return TrackRelayTeam
   */
  public function setOverallPlace($overallPlace)
  {
    $this->overallPlace = $overallPlace;
    return $this;
  }

  /**
   * @return bool
   */
  public function hasSetSchoolRecord()
  {
    return $this->setSchoolRecord;
  }

  /**
   * @param bool $setSchoolRecord
   * @return TrackRelayTeam
   */
  public function setHasSetSchoolRecord($setSchoolRecord)
  {
    $this->setSchoolRecord = $setSchoolRecord;
    return $this;
  }

}