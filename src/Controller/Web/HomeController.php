<?php


namespace Mavericks\Controller\Web;

use NuevaRunning\Service\Track\TrackService;
use Silex\Application;

class HomeController
{
  /**
   * @var Application
   */
  private $App;

  /**
   * @param Application $App
   * @param TrackService $TrackService
   */
  public function __construct(Application $App)
  {
    $this->App = $App;
  }

  /**
   * @return mixed
   */
  public function renderHome()
  {
    return $this->App['twig']->render('home.twig', array());
  }
}