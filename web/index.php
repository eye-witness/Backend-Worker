<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Eyewitness\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

include __DIR__.'/config.php';

// Basic App Setup Stuff
$app = new Application();
$app['debug'] = $debug;
$router = new Router($app);

// Register service providers
include __DIR__.'/../src/EyeWitness/registerProviders.php';

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json'))
    {
        $data = @json_decode($request->getContent(), true);

        if ($data === null)
        {
			$app->abort(400, 'Your request did not include valid JSON');
        }

        $request->request->replace(is_array($data) ? $data : array());

    }
});

$app->mount('/api/', $router->setApiRoutes());

$app->run();
