<?php

class mailer {
  private $config = NULL;
  public function __construct($c) {
    $this->config = $c;
  }
  public function compose() {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $this->config['host'];
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = $this->config['user'];
    $mail->Password = $this->config['pass'];
    return $mail;
  }
}

$container['email'] = function ($c) {
  return new mailer($c['settings']['gmail']);
};
?>
