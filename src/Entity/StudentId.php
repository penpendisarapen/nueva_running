<?php


namespace Mavericks\Entity;


use Mavericks\Exception\InvalidStudentIdException;

class StudentId
{
  /**
   * @var int
   */
  private $id;

  /**
   * @param $id
   * @throws InvalidStudentIdException
   */
  public function __construct($id)
  {
    if ($id instanceof StudentId)
    {
      return $id;
    }

    if (!is_int($id) && ctype_digit($id))
    {
      $id = (int)$id;
    }

    $this->throwOnInvalidId($id);

    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string)$this->id;
  }

  /**
   * @param $id
   * @throws InvalidStudentIdException
   */
  private function throwOnInvalidId($id)
  {
    if (!is_int($id))
    {
      throw new InvalidStudentIdException("$id must be an integer.");
    }

    if ($id < 1)
    {
      throw new InvalidStudentIdException("$id must be greater than 1.");
    }
  }

}