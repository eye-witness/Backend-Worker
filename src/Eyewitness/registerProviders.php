<?php

use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Provider\ValidatorServiceProvider;

$app->register(new ValidatorServiceProvider());
$app->register(new Silex\Provider\SerializerServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new DoctrineServiceProvider(), array(
	'dbs.options' => array(array(
		'driver'    => 'pdo_mysql',
		'host'      => 'localhost',
		'dbname'    => DB_NAME,
		'user'      => DB_USER,
		'password'  => DB_PASSWORD,
		'charset'   => 'utf8',
	)),
));
