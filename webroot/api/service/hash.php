<?php
// Default: bcrypt, 4096 iterations
$container['hash'] = function ($c) {
  return new \Phpass\Hash;
};
?>
