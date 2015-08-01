<?php

namespace Eyewitness\Test;

use Silex\WebTestCase as Base;

class ApiFunctionalTest extends Base
{
    public function createApplication()
    {
        $app = require $this->getApplicationDir() . '/app.php';
        unset($app['exception_handler']);

        return $app;
    }

    public function getApplicationDir()
    {
        return __DIR__.'/../../../app';
    }

    public function testPullApi()
    {
	    $client = $this->createClient();
		$client->request(
		    'POST',
		    '/api/appeals',
		    array(),
		    array(),
		    array('CONTENT_TYPE' => 'application/json'),
		    '{"blocks": [{"latitude":105,"longitude":5}],"time": 1438082766,"lastFetched": 1438082200}'
		);

		$this->assertTrue($client->getResponse()->isOk());

    }
}
