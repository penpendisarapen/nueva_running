<?php
set_include_path('src');
require_once __DIR__ . '/vendor/autoload.php';

use Mavericks\Controller\API\Provider\TrackRoutes as TrackAPI;
use Mavericks\Controller\Web\Provider\TrackRoutes as TrackWeb;
use Mavericks\Controller\Web\Provider\HomeRoutes;

$app = new Silex\Application();

$app['debug'] = true;

// register the necessary services and controllers
include __DIR__ . '/registry.php';

// mount the group routes

$app->mount("/", new HomeRoutes());
$app->mount("/api/track", new TrackAPI());
$app->mount("/track", new TrackWeb());

$app->run();
