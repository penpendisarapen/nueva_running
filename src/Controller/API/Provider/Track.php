<?php


namespace Mavericks\Controller\API\Provider;


use Silex\Application;
use Silex\ControllerProviderInterface;

class Track implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $track = $app["controllers_factory"];

    $track->get("/student/season/{season}", "controller.api.student:getAllBySeason");
    $track->get("/student/{id}", "controller.api.student:getOne");
    $track->get("/schedule", "controller.api.track.schedule:getCurrentSchedule");

    return $track;
  }

}