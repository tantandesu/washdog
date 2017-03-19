<?php
use Ramsey\Uuid\Uuid;

// POST /api/register
// email: CUHK email address
// password: user password
$app->post('/register', function($req, $resp) {
  $arg = $req->getParsedBody();
  if(parse_url($arg['email'], PHP_URL_HOST) !== 'link.cuhk.edu.hk') {
    return $resp->withJson(['error' => 'Invalid email'], 400);
  }
  $registrationKey = Uuid::uuid4()->getHex();
  $this->db->prepare('INSERT INTO user (email, password, registrationKey) VALUES (?,?,?)')->execute([
    $arg['email'],
    $this->hash->hashPassword($arg['password']),
    $registrationKey,
  ]);
  $mail = $this->email();
  $mail->addAddress($arg['email']);
  $mail->Subject = 'Washdog Registration';
  $mail->Body = '';
  if(!$mail->send()) {
    return $resp->withStatus(500);
  }
  return $resp;
});

// GET /api/verify/{base64Email}/{registrationKey}
$app->get('/verify/{base64Email}/{registrationKey}', function($req, $resp) {

});

?>
