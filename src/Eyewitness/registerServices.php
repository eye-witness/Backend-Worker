<?php

$app['police.data'] = $app->share(function() {
    return new PoliceDataUtils();
});

$app['api.controller'] = $app->share(function() use ($app) {
    return new ApiController($app['police.data'], $app['db']);
});
