<?php

if(empty($SESSION['key'])){
  $_SESSION['key'] = bin2hex(random_bytes(32));
}

$csrf = hash_hmac('sha256', 'index.php', $_SESSION['key']);

if (isset($_POST)) {
  if (hash_equals($csrf, $_POST['csrf'])) {

  } else {
    die('Invalid form submission. Error 44');
  }
}

 ?>
