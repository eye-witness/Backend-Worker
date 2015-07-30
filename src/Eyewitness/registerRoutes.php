<?php

$app->post('api/appeals/', 'api.controller:appealPostAction');
$app->put('api/appeals/', 'api.controller:appealPutAction');
