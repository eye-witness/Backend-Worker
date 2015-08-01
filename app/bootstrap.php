<?php

require_once(__DIR__.'/../vendor/silex.phar');

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespace('Eyewitness', __DIR__.'/../src');

$loader->register();

return $loader;
