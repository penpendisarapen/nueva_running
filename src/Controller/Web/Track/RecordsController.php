<?php


namespace Mavericks\Controller\Web\Track;


use Mavericks\Service\Track\MeetService;
use Mavericks\Service\Track\RecordsService;
use NuevaRunning\Entity\Grade;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RecordsController
{
  /**
   * @var Application
   */
  private $App;

  /**
   * @var RecordsService
   */
  private $RecordsService;

  /**
   * RecordsController constructor.
   * @param Application $App
   * @param RecordsService $RecordsService
   */
  public function __construct(Application $App, RecordsService $RecordsService)
  {
    $this->App            = $App;
    $this->RecordsService = $RecordsService;
  }

  /**
   * @return mixed
   */
  public function renderRecords(Request $Request)
  {
    $title = 'Nueva Mavericks';

    if ($Request->get('grade'))
    {
      $Grade   = new Grade($Request->get('grade'));
      $title   = $Grade->getGradeText();
      $records = $this->RecordsService->getSchoolRecordsByGrade($Grade);
    }
    else
    {
      $records = $this->RecordsService->getSchoolRecords();
    }

    return $this->App['twig']->render('Track/records.twig', array(
      'title'        => $title,
      'eventRecords' => $records
    ));
  }

  /**
   * @return mixed
   */
  public function renderAthleteRecords($studentId)
  {
    return $this->App['twig']->render('Track/athleteRecords.twig', array(
      'studentName'    => $this->RecordsService->getAthleteName($studentId),
      'athleteRecords' => $this->RecordsService->getAthleteRecords($studentId),
      'relayRecords'   => $this->RecordsService->getAthleteRelayRecords($studentId)
    ));
  }
}