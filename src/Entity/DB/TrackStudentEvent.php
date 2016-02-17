<?php

namespace Mavericks\Entity\DB;


class TrackStudentEvent
{
  /**
   * @var int
   */
  private $trackStudentEventId;

  /**
   * @var int
   */
  private $trackMeetId;

  /**
   * @var int
   */
  private $trackEventId;

  /**
   * @var int
   */
  private $studentId;

  /**
   * @var int
   */
  private $overallPlace;

  /**
   * @var bool
   */
  private $medaled = false;

  /**
   * @return mixed
   */
  public function getTrackStudentEventId()
  {
    return $this->trackStudentEventId;
  }

  /**
   * @param $trackStudentEventId
   * @return $this
   */
  public function setTrackStudentEventId($trackStudentEventId)
  {
    $this->trackStudentEventId = $trackStudentEventId;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getTrackMeetId()
  {
    return $this->trackMeetId;
  }

  /**
   * @param $trackMeetId
   * @return $this
   */
  public function setTrackMeetId($trackMeetId)
  {
    $this->trackMeetId = $trackMeetId;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getTrackEventId()
  {
    return $this->trackEventId;
  }

  /**
   * @param $trackEventId
   * @return $this
   */
  public function setTrackEventId($trackEventId)
  {
    $this->trackEventId = $trackEventId;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getStudentId()
  {
    return $this->studentId;
  }

  /**
   * @param $studentId
   * @return $this
   */
  public function setStudentId($studentId)
  {
    $this->studentId = $studentId;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getOverallPlace()
  {
    return $this->overallPlace;
  }

  /**
   * @param $overallPlace
   * @return $this
   */
  public function setOverallPlace($overallPlace)
  {
    $this->overallPlace = $overallPlace;

    return $this;
  }

  /**
   * @return bool
   */
  public function hasMedaled()
  {
    return $this->medaled;
  }

  /**
   * @param $medaled
   * @return $this
   */
  public function setHasMedaled($medaled)
  {
    $this->medaled = $medaled ? true : false;

    return $this;
  }
  
  
}