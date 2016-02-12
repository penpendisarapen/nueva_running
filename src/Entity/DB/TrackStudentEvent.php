<?php

namespace Mavericks\Entity\DB;


class TrackStudentEvent
{
  private $trackStudentEventId;
  
  private $trackMeetId;
  
  private $trackEventId;
  
  private $studentId;
  
  private $overallPlace;
  
  private $medaled;

  public function __construct()
  {
  }

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
   * @return mixed
   */
  public function medaled()
  {
    return $this->medaled;
  }

  /**
   * @param $medaled
   * @return $this
   */
  public function setMedaled($medaled)
  {
    $this->medaled = $medaled ? true : false;

    return $this;
  }
  
  
}