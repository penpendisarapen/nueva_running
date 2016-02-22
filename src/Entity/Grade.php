<?php


namespace NuevaRunning\Entity;


use Mavericks\Exception\InvalidGradeException;

class Grade
{
  const FRESHMAN  = 9;
  const SOPHOMORE = 10;
  const JUNIOR    = 11;
  const SENIOR    = 12;

  private $validGrades = array(
    self::FRESHMAN  => 'Freshman',
    self::SOPHOMORE => 'Sophomore',
    self::JUNIOR    => 'Junior',
    self::SENIOR    => 'Senior'
  );

  /**
   * @var int
   */
  private $grade;

  /**
   * Grade constructor.
   * @param $grade
   */
  public function __construct($grade)
  {
    if ($grade instanceof Grade)
    {
      return $grade;
    }

    if (!is_int($grade) && ctype_digit($grade))
    {
      $grade = (int)$grade;
    }

    $this->throwOnInvalidGrade($grade);

    $this->grade = $grade;
  }

  /**
   * @return int
   */
  public function getGrade()
  {
    return $this->grade;
  }

  /**
   * @return string
   */
  public function getGradeText()
  {
    return $this->validGrades[$this->grade];
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getGradeText();
  }

  /**
   * @param $grade
   * @throws InvalidGradeException
   */
  private function throwOnInvalidGrade($grade)
  {
    if (!is_int($grade) || !array_key_exists($grade, $this->validGrades))
    {
      throw new InvalidGradeException("Grade must be an integer between 9 and 12.");
    }
  }
}