<?php


namespace Mavericks\Controller\Web\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class TrackRoutes implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $track = $app["controllers_factory"];

    $track->get("/", "controller.web.track.home:renderHome");
    $track->get("/schedule/", "controller.web.track.meet:renderCurrentSeasonSchedule");
    $track->get("/athletes/", "controller.web.track.meet:renderAthletes");
    $track->get("/records/", "controller.web.track.records:renderRecords");
    $track->get("/meet/results/{meetId}/", "controller.web.track.meet:renderMeetResults");
    $track->get("/admin/event/{meetId}/", "controller.web.track.admin:getAthleteEventEntry");
    $track->post("/admin/event/{meetId}/", "controller.web.track.admin:postAthleteEventEntry");

    return $track;
  }

}