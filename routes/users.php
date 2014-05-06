<?php

$anchor = '/user';


$app->get($anchor, function () use ($app, $system) {


});

$app->get($anchor . '/:id', function ($id) use ($app, $system) {

});

$app->post($anchor . '/:id', function ($id) use ($app, $system) {});

$app->put($anchor . '/:id', function ($id) use ($app, $system) {});

$app->delete($anchor . '/:id', function ($id) use ($app, $system) {});


?>