<?php


namespace NuevaRunning\Controller\API;



use NuevaRunning\Entity\Season;
use NuevaRunning\Entity\StudentId;
use NuevaRunning\Repository\StudentService;
use Silex\Application;

class StudentController
{
  /**
   * @var Application
   */
  private $App;

  /**
   * @var StudentService
   */
  private $StudentService;

  public function __construct(Application $App, StudentService $StudentService)
  {
    $this->App            = $App;
    $this->StudentService = $StudentService;
  }

  public function getAllBySeason($season)
  {
    return $this->StudentService->getStudentsBySeason(new Season($season));
  }

  public function getOne($id)
  {
    return $this->StudentService->getStudentById(new StudentId($id));
  }

  public function getStudentResultsByMeet()
  {}

  public function getStudentResultsBySeason()
  {}

}