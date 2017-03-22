<?php
use Ramsey\Uuid\Uuid;

// POST /api/register
// email: CUHK email address
// password: user password
$app->post('/register', function($req, $resp) {
  $arg = $req->getParsedBody();
  if(substr(strrchr($arg['email'], "@"), 1) !== 'link.cuhk.edu.hk') {
    return $resp->withJson(['error' => 'Invalid email'], 400);
  }
  $registrationKey = Uuid::uuid4()->getHex();
  $this->db->prepare('INSERT INTO user (email, password, registrationKey) VALUES (?,?,?)')->execute([
    $arg['email'],
    $this->hash->hashPassword($arg['password']),
    $registrationKey,
  ]);
  $baseUrl = $req->getUri()->getBaseUrl();
  $userId = $this->db->lastInsertId();
  $mail = $this->email->compose();
  $mail->addAddress($arg['email']);
  $mail->Subject = 'Washdog Registration';
  $mail->Body =
    "Click on the following link to activate your account:\n".
    "$baseUrl/verify/$userId/$registrationKey";
  if(!$mail->send()) {
    $this->db->exec("DELETE FROM user WHERE id = $userId");
    return $resp->withJson(['error' => $mail->ErrorInfo], 500);
  }
  return $resp;
});

// GET /api/verify/{userId}/{registrationKey}
$app->get('/verify/{userId}/{registrationKey}', function($req, $resp) {
  $userId = $req->getAttribute('userId');
  $registrationKey = $req->getAttribute('registrationKey');
  $setVerified = $this->db->prepare(
    'UPDATE user SET verified = TRUE WHERE id = ? AND registrationKey = ?');
  $setVerified->execute([$userId, $registrationKey]);
  if($setVerified->rowCount() < 1) {
    $resp->getBody()->write('Error: invalid activation link.');
    return $resp->withStatus(400);
  }
  $resp->getBody()->write('User activated.');
  return $resp;
});

?>
