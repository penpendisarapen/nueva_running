<?php

namespace Maverics\Entity\DB;

class TrackRelayTeamMember
{
  /**
   * @var int
   */
  private $trackRelayTeamId;

  /**
   * @var int
   */
  private $studentId;

  /**
   * @var int
   */
  private $individualDistance;

  /**
   * @var float
   */
  private $splitTime;

  /**
   * @return int
   */
  public function getTrackRelayTeamId()
  {
    return $this->trackRelayTeamId;
  }

  /**
   * @param int $trackRelayTeamId
   * @return TrackRelayTeamMember
   */
  public function setTrackRelayTeamId($trackRelayTeamId)
  {
    $this->trackRelayTeamId = $trackRelayTeamId;
    return $this;
  }

  /**
   * @return int
   */
  public function getStudentId()
  {
    return $this->studentId;
  }

  /**
   * @param int $studentId
   * @return TrackRelayTeamMember
   */
  public function setStudentId($studentId)
  {
    $this->studentId = $studentId;
    return $this;
  }

  /**
   * @return int
   */
  public function getIndividualDistance()
  {
    return $this->individualDistance;
  }

  /**
   * @param int $individualDistance
   * @return TrackRelayTeamMember
   */
  public function setIndividualDistance($individualDistance)
  {
    $this->individualDistance = $individualDistance;
    return $this;
  }

  /**
   * @return float
   */
  public function getSplitTime()
  {
    return $this->splitTime;
  }

  /**
   * @param float $splitTime
   * @return TrackRelayTeamMember
   */
  public function setSplitTime($splitTime)
  {
    $this->splitTime = $splitTime;
    return $this;
  }
  
  
}