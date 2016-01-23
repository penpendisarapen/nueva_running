<?php


namespace NuevaRunning\Controller\Web\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Track implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $track = $app["controllers_factory"];

    $track->get("/schedule", "controller.web.track.schedule:renderCurrentSeasonSchedule");

    return $track;
  }

}