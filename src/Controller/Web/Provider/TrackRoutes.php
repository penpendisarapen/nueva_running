<?php


namespace Mavericks\Controller\Web\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class TrackRoutes implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $track = $app["controllers_factory"];

    $track->get("/", "controller.web.track.meet:renderHome");
    $track->get("/schedule/", "controller.web.track.meet:renderCurrentSeasonSchedule");
    $track->get("/athletes/", "controller.web.track.meet:renderAthletes");
    $track->get("/records/", "controller.web.track.records:renderRecords");

    return $track;
  }

}