<?php


namespace Mavericks\Controller\Web\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class HomeRoutes implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $site = $app["controllers_factory"];

    $site->get("/", "controller.web.site:renderHome");

    return $site;
  }

}