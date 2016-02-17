<?php


namespace Mavericks\Entity\DB;

class TrackEvent
{
  private $trackEventId;

  private $trackMeetId;

  private $trackEventTypeId;

  private $eventGender;

  private $eventSubType = '';

  /**
   * @var Time
   */
  private $eventStartTime;

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
  public function getTrackEventTypeId()
  {
    return $this->trackEventTypeId;
  }

  /**
   * @param $trackEventTypeId
   * @return $this
   */
  public function setTrackEventTypeId($trackEventTypeId)
  {
    $this->trackEventTypeId = $trackEventTypeId;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getEventGender()
  {
    return $this->eventGender;
  }

  /**
   * @param $eventGender
   * @return $this
   */
  public function setEventGender($eventGender)
  {
    $this->eventGender = $eventGender;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getEventSubType()
  {
    return $this->eventSubType;
  }

  /**
   * @param $eventSubType
   * @return $this
   */
  public function setEventSubType($eventSubType)
  {
    $this->eventSubType = $eventSubType;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getEventStartTime()
  {
    return $this->eventStartTime;
  }

  /**
   * @param $eventStartTime
   * @return $this
   */
  public function setEventStartTime(Time $eventStartTime)
  {
    $this->eventStartTime = $eventStartTime;

    return $this;
  }

  public function validateForInsert()
  {}

  public function validateForUpdate()
  {}
}