<?php


namespace Mavericks\Controller\Web\Track;

use Mavericks\Service\Track\TrackService;
use Silex\Application;

class HomeController
{
  /**
   * @var Application
   */
  private $App;

  private $TrackService;

  /**
   * @param Application $App
   * @param TrackService $TrackService
   */
  public function __construct(Application $App, TrackService $TrackService)
  {
    $this->App          = $App;
    $this->TrackService = $TrackService;
  }

  /**
   * @return mixed
   */
  public function renderHome()
  {
    return $this->App['twig']->render('Track/home.twig', array(
      'nextMeet'      => $this->TrackService->getUpcomingMeet(),
      'announcements' => $this->TrackService->getLatestAnnouncements()
    ));
  }

}