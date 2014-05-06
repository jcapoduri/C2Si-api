<?php
// CORS enable
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With');
ini_set('display_errors', 1);

date_default_timezone_set('UTC');

// SLIM Framework, to provide REST capability and scalfolding
require_once 'libs/Slim/Slim.php';
// small propietary ORM
require_once 'libs/RedBean/rb.phar';
// small propietary Auth
require_once 'libs/Auth/Auth.php';
require_once 'libs/Auth/AuthMiddleware.php';

require_once 'libs/Respect/Validation/Validator.php';

use Respect\Validation\Validator as v;


// SLIM setting up
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

error_reporting(E_ALL);

//set Auth middleware
//$app->add(new AuthMiddleware());

//R::setup('mysql:host=localhost;dbname=blackfeather', 'root', '');
R::setup('mysql:host=127.4.70.129;dbname=c9', 'jcapoduri', '');
//R::nuke();
// Noop - no operation (for testing)
$app->get('/noop', function () use ($app){
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response->write('{}');
});

require_once 'security/access.php';
require_once 'security/register.php';

//R::freeze(TRUE);


// SLIM start point
$app->run();


?>
