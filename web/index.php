<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Eyewitness\Router;

include __DIR__.'/config.php';

// Basic App Setup Stuff
$app = new Application();
$app['debug'] = $debug;
$router = new Router($app);

// Register service providers
include __DIR__.'/../src/EyeWitness/registerProviders.php';

$app->mount('/', $router->setBasicRoutes());

$app->run();
