<?php


namespace Mavericks\Entity;


use Mavericks\Exception\InvalidMeetType;

class MeetType
{
  const TYPE_TRACK = 'track';
  const TYPE_CROSS_COUNTRY = 'cross country';

  /**
   * @var string
   */
  private $meetType;

  private $validTypes = array(
    self::TYPE_TRACK,
    self::TYPE_CROSS_COUNTRY
  );

  /**
   * @param $type string
   * @throws InvalidMeetType
   */
  public function __construct($type)
  {
    if ($type instanceof MeetType)
    {
      return $type;
    }

    $type = strtolower($type);

    $this->throwOnInvalidType($type);

    $this->meetType = $type;
  }

  /**
   * @return string
   */
  public function getMeetType()
  {
    return $this->meetType;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->meetType;
  }

  /**
   * @param $type string
   * @throws InvalidMeetType
   */
  private function throwOnInvalidType($type)
  {
    if (!in_array($type, $this->validTypes))
    {
      throw new InvalidMeetType('Invalid meet type.');
    }
  }
}