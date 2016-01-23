<?php

use NuevaRunning\ObjectFactory;
use NuevaRunning\Controller\API\Track\StudentController;
use NuevaRunning\Controller\API\Track\ScheduleController as TrackScheduleAPIController;
use NuevaRunning\Controller\Web\Track\ScheduleController as TrackScheduleWebController;

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