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
  private $ScheduleService;

  public function __construct(Application $App, RecordsService $RecordsService)
  {
    $this->App             = $App;
    $this->ScheduleService = $RecordsService;
  }

  public function renderRecords()
  {
    return $this->App['twig']->render('Track/records.twig', array());
  }

}