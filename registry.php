<?php

use Mavericks\ObjectFactory;
use Mavericks\Controller\Web\Track\HomeController;
use Mavericks\Controller\API\Track\StudentController;
use Mavericks\Controller\API\Track\ScheduleController as TrackMeetAPIController;
use Mavericks\Controller\Web\Track\MeetController as TrackMeetWebController;
use Mavericks\Controller\Web\Track\RecordsController as TrackRecordsWebController;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/views'));

$Factory = new ObjectFactory();

$app['service.student'] = $app->share(function() use ($Factory) {
  return $Factory->createStudentService();
});

$app['service.track.meet'] = $app->share(function() use ($Factory) {
  return $Factory->createMeetService();
});

$app['service.track.records'] = $app->share(function() use ($Factory) {
  return $Factory->createRecordsService();
});


$app['controller.web.site'] = $app->share(function() use ($app) {
  return new HomeController($app);
});

$app['controller.api.student'] = $app->share(function() use ($app) {
  return new StudentController($app, $app['service.student']);
});

$app['controller.api.track.meet'] = $app->share(function() use ($app) {
  return new TrackMeetAPIController($app, $app['service.track.meet']);
});

$app['controller.web.track.meet'] = $app->share(function() use ($app) {
  return new TrackMeetWebController($app, $app['service.track.meet']);
});

$app['controller.web.track.records'] = $app->share(function() use ($app) {
  return new TrackRecordsWebController($app, $app['service.track.records']);
});