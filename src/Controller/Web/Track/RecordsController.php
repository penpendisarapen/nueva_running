<?php


namespace Mavericks\Controller\Web\Track;


use Mavericks\Service\Track\MeetService;
use Mavericks\Service\Track\RecordsService;
use Silex\Application;

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
  public function renderRecords()
  {
    return $this->App['twig']->render('Track/records.twig', array(
      'eventRecords' => $this->RecordsService->getSchoolRecords()
    ));
  }

}