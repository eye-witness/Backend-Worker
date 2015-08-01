<?php

namespace Eyewitness\Test;

use Silex\WebTestCase as Base;

class ApiFunctionalTest extends Base
{
    public function createApplication()
    {
        $app = require $this->getApplicationDir().'/app.php';

        return $app;
    }

    public function getApplicationDir()
    {
        return __DIR__.'/../app/app.php';
    }
}
