<?php
require '../../vendor/autoload.php';

require 'config.php';
$app = new \Slim\App($config);
$container = $app->getContainer();

// services
require 'service/database.php';
require 'service/email.php';

// routes

$app->get('/hello/{name}', function($req, $resp) {
  $name = $req->getAttribute('name');
  return $resp->withJson([
    'message' => "Hello, $name"
  ]);
});

require 'route/authentication.php';
require 'route/registration.php';

$app->run();

?>
