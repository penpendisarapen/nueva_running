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
    $track->get("/schedule/{season}/", "controller.web.track.meet:renderSeasonSchedule");
    $track->get("/schedule/", "controller.web.track.meet:renderCurrentSeasonSchedule");
    $track->get("/athletes/", "controller.web.track.meet:renderAthletes");

    $track->get("/records/", "controller.web.track.records:renderRecords");
    $track->get("/records/athlete/{studentId}", "controller.web.track.records:renderAthleteRecords");

    $track->get("/meet/results/{meetId}/", "controller.web.track.meet:renderMeetResults");
    $track->get("/resources/", "controller.web.track.meet:renderResources");


    $track->get("/admin/", "controller.web.track.admin:getAdminHome");

    $track->get("/admin/athlete", "controller.web.track.admin:getAthleteEntry");
    $track->post("/admin/athlete/list", "controller.web.track.admin:postAthleteListEntry");
    $track->post("/admin/athlete/new", "controller.web.track.admin:postNewAthleteEntry");

    $track->get("/admin/meet", "controller.web.track.admin:getMeetEntry");
    $track->post("/admin/meet", "controller.web.track.admin:postMeetEntry");

    $track->get("/admin/meet/{meetId}/", "controller.web.track.admin:getMeetEventEntry");
    $track->post("/admin/meet/{meetId}/", "controller.web.track.admin:postMeetEventEntry");

    $track->get("/admin/meet/{meetId}/event/", "controller.web.track.admin:getEventEntry");
    $track->post("/admin/meet/{meetId}/event/", "controller.web.track.admin:postEventEntry");

    $track->get("/admin/meet/{meetId}/event/{eventId}/", "controller.web.track.admin:getEditEventEntry");
    $track->post("/admin/meet/{meetId}/event/{eventId}/athlete/", "controller.web.track.admin:postEventAthlete");
    $track->post("/admin/meet/{meetId}/event/{eventId}/delete/", "controller.web.track.admin:deleteMeetEvent");

    $track->post("/admin/meet/{meetId}/event/{eventId}/result/", "controller.web.track.admin:postEventAthleteResult");

    $track->post("/admin/meet/{meetId}/athlete/event/{trackStudentEventId}/", "controller.web.track.admin:deleteAthleteEntry");

    return $track;
  }

}