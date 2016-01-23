<?php


namespace NuevaRunning\Repository;


use NuevaRunning\Entity\Season;
use NuevaRunning\Entity\StudentId;
use NuevaRunning\Persistence\SQLPersistence;

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