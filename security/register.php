<?php

$app->post('/register/user', function() use ($app) {
    $response = $app->response();
	$response['Content-Type'] = 'application/json';

    $json = json_decode($app->request()->getBody(), true);

    //check for all values
    $ok = true;
    $ok = $ok && isset($json['username']);
    $ok = $ok && isset($json['password']);
    $ok = $ok && isset($json['password2']);
    $ok = $ok && isset($json['email']);
    $ok = $ok && isset($json['email2']);

    if (!$ok){
        $app->response()->status(500);
        $app->response()->write(response::fail("invalid JSON")->toJson());
        return;
    };

    //validatiokn
    $err_validation = "";
    if ($json['password'] !== $json['password2'])  $err_validation = "Passwords no coinciden";
    if ($json['email'] !== $json['email2'])  $err_validation = "emails no coinciden";

    if ($err_validation !== "") {
        $app->response()->status(500);
        $app->response()->write(\nd\response::fail($err_validation)->toJson());
        return;
    };

    // all ok, let persist
    $user = R::dispense('user');
    $user->username = $json["username"];
    $user->email = $json['email'];
    $user->password = md5($json["password"]);
    $user->token = md5($user->cuit . $user->email);
    $user->expiration = date(DateTime::ISO8601, time() + (7 * 24 * 60 * 60)); //one week

	R::begin();

    $id = R::store($user);

	if ($id) {
		R::commit();
		$app->response()->write($user->token);
	} else {
        R::rollback();
		$app->response()->status(500);
        $app->response()->write(response::fail("persist failed")->toJson());
	};

});

$app->get('/register/user/:token', function($token) use ($app) {
    $response = $app->response();
    $response['Content-Type'] = 'application/json';

    $token = R::findOne('user', 'token LIKE ?', array($token));
    if ($token) {
        $app->response()->write();
    } else {
        $app->response()->status(500);
        $app->response()->write(response::fail("invalid token")->toJson());
    };
});

/*$app->post('/register/user', function() use ($app) {
    $response = $app->response();
    $response['Content-Type'] = 'application/json';

    $json = json_decode($app->request()->getBody(), true);
    $user = R::dispense('user');
    $user->import($json);

    $token = R::findOne('user', 'token LIKE ? AND expiration > now()', array($user->token));
    if (!$token) {
        $app->response()->status(500);
        $app->response()->write(response::fail("invalid token")->toJson());
        return;
    };

    unset($user->token);

    $id = R::store($user);
    $app->response()->write($id);
});*/

?>