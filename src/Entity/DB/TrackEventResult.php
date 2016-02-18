<?php


namespace NuevaRunning\Entity\DB;

use Mavericks\Entity\ResultTime;
use Mavericks\Entity\ResultMeasurement;

class TrackEventResult
{
  /**
   * @var int
   */
  private $trackEventResultId;

  /**
   * @var int
   */
  private $trackStudentEventId;

  /**
   * @var int
   */
  private $heatNumber;

  /**
   * @var ResultTime
   */
  private $resultInSeconds;

  /**
   * @var ResultMeasurement
   */
  private $resultInInches;

  /**
   * @var int
   */
  private $place;

  /**
   * @var bool
   */
  private $isFinal;

  /**
   * @var bool
   */
  private $setSchoolRecord;

  /**
   * @var bool
   */
  private $setPersonalRecord;

  /**
   * @return int
   */
  public function getTrackEventResultId()
  {
    return $this->trackEventResultId;
  }

  /**
   * @param int $trackEventResultId
   * @return TrackEventResult
   */
  public function setTrackEventResultId($trackEventResultId)
  {
    $this->trackEventResultId = $trackEventResultId;
    return $this;
  }

  /**
   * @return int
   */
  public function getTrackStudentEventId()
  {
    return $this->trackStudentEventId;
  }

  /**
   * @param int $trackStudentEventId
   * @return TrackEventResult
   */
  public function setTrackStudentEventId($trackStudentEventId)
  {
    $this->trackStudentEventId = $trackStudentEventId;
    return $this;
  }

  /**
   * @return int
   */
  public function getHeatNumber()
  {
    return $this->heatNumber;
  }

  /**
   * @param int $heatNumber
   * @return TrackEventResult
   */
  public function setHeatNumber($heatNumber)
  {
    $this->heatNumber = $heatNumber;
    return $this;
  }

  /**
   * @return ResultTime
   */
  public function getResultInSeconds()
  {
    return $this->resultInSeconds;
  }

  /**
   * @param ResultTime $ResultTime
   * @return TrackEventResult
   */
  public function setResultInSeconds(ResultTime $ResultTime)
  {
    $this->resultInSeconds = $ResultTime;
    return $this;
  }

  /**
   * @return ResultMeasurement
   */
  public function getResultInInches()
  {
    return $this->resultInInches;
  }

  /**
   * @param ResultMeasurement $result
   * @return TrackEventResult
   */
  public function setResultInInches($result)
  {
    $this->resultInInches = $result;
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
   * @return TrackEventResult
   */
  public function setPlace($place)
  {
    $this->place = $place;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isFinal()
  {
    return $this->isFinal;
  }

  /**
   * @param boolean $isFinal
   * @return TrackEventResult
   */
  public function setIsFinal($isFinal)
  {
    $this->isFinal = $isFinal;
    return $this;
  }

  /**
   * @return boolean
   */
  public function hasSetSchoolRecord()
  {
    return $this->setSchoolRecord;
  }

  /**
   * @param boolean $setSchoolRecord
   * @return TrackEventResult
   */
  public function setHasSetSchoolRecord($setSchoolRecord)
  {
    $this->setSchoolRecord = $setSchoolRecord ? true : false;
    return $this;
  }

  /**
   * @return boolean
   */
  public function hasSetPersonalRecord()
  {
    return $this->setPersonalRecord;
  }

  /**
   * @param boolean $setPersonalRecord
   * @return TrackEventResult
   */
  public function setHasSetPersonalRecord($setPersonalRecord)
  {
    $this->setPersonalRecord = $setPersonalRecord ? true : false;
    return $this;
  }

}