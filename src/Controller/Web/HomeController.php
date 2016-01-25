<?php


namespace Mavericks\Controller\Web\Track;

use Silex\Application;

class HomeController
{
  /**
   * @var Application
   */
  private $App;

  public function __construct(Application $App)
  {
    $this->App = $App;
  }

  public function renderHome()
  {
    return $this->App['twig']->render('home.twig', array());
  }
}