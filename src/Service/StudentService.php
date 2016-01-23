<?php


namespace Mavericks\Repository;


use Mavericks\Entity\Season;
use Mavericks\Entity\StudentId;
use Mavericks\Persistence\SQLPersistence;

class StudentService
{
  /**
   * @var SQLPersistence
   */
  private $Persistence;

  public function __construct(SQLPersistence $Persistence)
  {
    $this->Persistence = $Persistence;
  }

  public function getStudentById(StudentId $StudentId)
  {
    return "STUDENT: " . $StudentId->getId();
  }

  public function getStudentsBySeason(Season $Season)
  {
    return "STUDENTS FOR SEASON " . $Season;
  }

}