<?php

use Mavericks\ObjectFactory;
use Mavericks\Controller\API\Track\StudentController;
use Mavericks\Controller\API\Track\ScheduleController as TrackScheduleAPIController;
use Mavericks\Controller\Web\Track\ScheduleController as TrackScheduleWebController;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/views'));

$Factory = new ObjectFactory();

$app['service.student'] = $app->share(function() use ($Factory) {
  return $Factory->createStudentService();
});

$app['service.track.schedule'] = $app->share(function() use ($Factory) {
  return $Factory->createScheduleService();
});

$app['controller.api.student'] = $app->share(function() use ($app) {
  return new StudentController($app, $app['service.student']);
});

$app['controller.api.track.schedule'] = $app->share(function() use ($app) {
  return new TrackScheduleAPIController($app, $app['service.track.schedule']);
});

$app['controller.web.track.schedule'] = $app->share(function() use ($app) {
  return new TrackScheduleWebController($app, $app['service.track.schedule']);
});