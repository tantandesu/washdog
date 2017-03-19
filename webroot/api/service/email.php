<?php
$container['email'] = function ($c) {
  $gmail = $c['settings']['gmail'];
  return function () {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $gmail['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $gmail['user'];
    $mail->Password = $gmail['pass'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    return $mail;
  };
};
?>
