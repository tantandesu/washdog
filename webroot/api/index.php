<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../vendor/autoload.php';

require 'config.php';
$app = new \Slim\App($config);
$container = $app->getContainer();

// services
require 'service/db.php';
require 'service/email.php';
require 'service/hash.php';

// routes
require 'route/authentication.php';
require 'route/registration.php';
require 'route/StateInquiry.php';

// hello world
$app->get('/hello/{name}', function($req, $resp) {
  $name = $req->getAttribute('name');
  return $resp->withJson([
    'message' => "Hello, $name"
  ]);
});

$app->run();

?>
