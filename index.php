<?php
set_include_path('src');
require_once __DIR__ . '/vendor/autoload.php';

use Mavericks\Controller\API\Provider\Track as TrackAPI;
use Mavericks\Controller\Web\Provider\Track as TrackWeb;

$app = new Silex\Application();

$app['debug'] = true;

include __DIR__ . '/registry.php';

$app->mount("/api/track", new TrackAPI());
$app->mount("/track", new TrackWeb());

$app->run();
