function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if(empty($SESSION['key'])){
  $_SESSION['key'] = bin2hex(generateRandomString());
}

$csrf = hash_hmac('sha256', 'header.php', $_SESSION['key']);

if (!empty(isset($_POST))) {
  if (hash_equals($csrf, $_POST['csrf'])) {

  } else {
    die('Invalid form submission. Error 44');
    header('Location: /');
  }
}
