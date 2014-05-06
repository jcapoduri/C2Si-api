<?php

$anchor = '/apps/';

/*
* GET: return new instance of application for the current user
*/
$app->get($anchor, function () use ($app, $system) {
    //$query = $system
    $app->response['Content-Type'] = 'application/json';
    $query = $system->query("applications");
});


?>