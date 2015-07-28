<?php

namespace Eyewitness;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Router
{
	public $app;

	function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function setBasicRoutes()
	{
		$controllerFactory = $this->app['controllers_factory'];

		return $controllerFactory;
	}
}
