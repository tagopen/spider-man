<?php
  if (is_file('lib/class.phpmailer.php')) {
    require_once("lib/class.phpmailer.php");
  }
  if (is_file('lib/class.smtp.php')) {
    require_once("lib/class.smtp.php");
  }

  $http_host = $_SERVER['HTTP_HOST'];
  $body = '';
  if ( substr($http_host, 0, 4)=='www.') {
    $host_name = substr($http_host, 4);
  } else {
    $host_name = $http_host;
  }
  if (isset($_SERVER['HTTP_REFERER'])) {
    $http_referer = $_SERVER['HTTP_REFERER'];
  } else {
    $http_referer = '';
  }
  define ('HTTP_SERVER', 'http://' . $http_host . '/');
  define ('HOST_NAME', $host_name);
  define ('HTTP_REFERER', $http_referer);
  $post = array( 
    'host_name'     => HOST_NAME,
    'host_dir'      => HTTP_SERVER,
    'host_referer'  => HTTP_REFERER
    );
  if (!empty($_POST["form"])) {
    foreach( $_POST["form"] as $key => $value) { 
      $post['user_form'] = $key;
      $body .= 'Форма: ' . $post['user_form'] . chr(10) . chr(13);
    }
  }
  if (!empty($_POST["email"])) {
    $post['user_email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $body .= 'Почта: ' . $post['user_email'] . chr(10) . chr(13);
  }

  if (!empty($_POST["date"])) {
    $post['user_date'] = filter_input(INPUT_POST,'date', FILTER_SANITIZE_STRING);
    $body .= 'Дата рождения: ' . $post['user_date'] . chr(10) . chr(13);
  }

  if (!empty($_POST["gender"])) {
    $post['user_gender'] = filter_input(INPUT_POST,'gender', FILTER_SANITIZE_STRING);
    $body .= 'Пол: ' . $post['user_gender'] . chr(10) . chr(13);
  }

  if (!empty($_POST["cinema"])) {
    $post['user_cinema'] = filter_input(INPUT_POST, 'cinema', FILTER_SANITIZE_STRING);
    $body .= 'Кинотеатр: ' . $post['user_cinema'] . chr(10) . chr(13);
  }

  if (!empty($_POST["online"])) {
    $post['user_online'] = filter_input(INPUT_POST, 'online', FILTER_SANITIZE_STRING);
    $body .= 'Покупка: ' . $post['user_online'] . chr(10) . chr(13);
  }

  if (!empty($_POST["promocode"])) {
    $post['user_promocode'] = filter_input(INPUT_POST, 'promocode', FILTER_SANITIZE_STRING);
    $body .= 'Промо-код: ' . $post['user_promocode'] . chr(10) . chr(13);
  }

  if (!empty($_POST["ticket1"])) {
    $post['user_ticket1'] = filter_input(INPUT_POST, 'ticket1', FILTER_SANITIZE_STRING);
    $body .= 'Билет №1: ' . $post['user_ticket1'] . chr(10) . chr(13);
  }

  if (!empty($_POST["ticket2"])) {
    $post['user_ticket2'] = filter_input(INPUT_POST, 'ticket2', FILTER_SANITIZE_STRING);
    $body .= 'Билет №2: ' . $post['user_ticket2'] . chr(10) . chr(13);
  }

  if (!empty($_POST["product_name"])) {
    foreach( $_POST["product_name"] as $key => $value){ 
      $post['product_name'] = $key;
      $body .= 'Название товара: ' . $post['product_name'] . chr(10) . chr(13);
    }
  }

  $body .= chr(10) . chr(13) . "С уважением," . chr(10) . chr(13) . "разработчики сайта " . $post['host_referer'];

  $mail = new PHPMailer();
  $mail->CharSet      = 'UTF-8';
  $mail->IsSendmail();

  $from = 'no-repeat@tagopen.com';
  $to = "Artem2431@gmail.com";
  $mail->SetFrom($from, HOST_NAME);
  $mail->AddAddress($to);
  $mail->isHTML(false);
  $mail->Subject      = "Новая заявка";
  $mail->Body         = $body;

  if(!$mail->send()) {
    echo 'Что-то пошло не так. ' . $mail->ErrorInfo;
    return false;
  } else {
    echo 'Форма успешно отправлена';
    return true;
  }

?>