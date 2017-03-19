<?php
use Ramsey\Uuid\Uuid;

// POST /api/login
// email: user email address
// password: user password
$app->post('/login', function($req, $resp) {
  $arg = $req->getParsedBody();
  $selectUser = $this->db->prepare('SELECT id,password FROM user WHERE email = ?');
  $selectUser->execute([$arg['email']]);
  $user = $selectUser->fetch();
  if(!$user) {
    return $resp->withJson(['error' => 'Invalid email'], 401);
  }
  if(!$this->hash->checkPassword($arg['password'], $user['password'])) {
    return $resp->withJson(['error' => 'Invalid password'], 401);
  }
  $apiKey = Uuid::uuid4()->getHex(); // 128-bit hex string
  $this->db->prepare('INSERT INTO access (apiKey, userId) VALUES (?, ?)')->execute([
    $apiKey,
    $user['id']
  ]);
  return $resp->withJson([
    'email' => $arg['email'],
    'apiKey' => $apiKey,
  ]);
});

// POST /api/logout
// apiKey: the API key to invalidate
$app->post('/logout', function($req, $resp) {
  $arg = $req->getParsedBody();
  $voidKey = $this->db->prepare('UPDATE access SET void = TRUE WHERE apiKey = ?');
  $voidKey->execute([$arg['apiKey']]);
  if($voidKey->rowCount() < 1) {
    return $resp->withStatus(400);
  }
  return $resp;
});

?>
