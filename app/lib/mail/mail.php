<?php
  if (is_file('lib/class.phpmailer.php')) {
    require_once("lib/class.phpmailer.php");
  }
  if (is_file('lib/class.smtp.php')) {
    require_once("lib/class.smtp.php");
  }

class MailClass {
  function sendMail() {

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

    if (!empty($_POST["form"]) && isset($_POST["form"])) {
      foreach( $_POST["form"] as $key => $value) { 
        $post['user_form'] = $key;
        $body .= 'Форма: ' . $post['user_form'] . chr(10) . chr(13);
      }
    }

    if (!empty($_POST["email"]) && isset($_POST["email"])) {
      $post['user_email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $body .= 'Email: ' . $post['user_email'] . chr(10) . chr(13);
    }

    if (!empty($_POST["cinema"]) && isset($_POST["cinema"])) {
      $post['user_cinema'] = filter_input(INPUT_POST, 'cinema', FILTER_SANITIZE_EMAIL);
      $body .= 'Кинотеатр: ' . $post['user_cinema'] . chr(10) . chr(13);
    }

    if (!empty($_POST["purchase"]) && isset($_POST["purchase"])) {
      $post['user_purchase'] = filter_input(INPUT_POST, 'purchase', FILTER_SANITIZE_EMAIL);
      $body .= 'Свособ оплаты: ' . $post['user_purchase'] . chr(10) . chr(13);
    }

    if (!empty($_POST["promo"]) && isset($_POST["promo"])) {
      $post['user_promo'] = filter_input(INPUT_POST, 'promo', FILTER_SANITIZE_EMAIL);
      $body .= 'Промо код: ' . $post['user_promo'] . chr(10) . chr(13);
    }

    if (!empty($_POST["ticket_1"]) && isset($_POST["ticket_1"])) {
      $post['user_ticket_1'] = filter_input(INPUT_POST, 'ticket_1', FILTER_SANITIZE_EMAIL);
      $body .= 'Билет №1: ' . $post['user_ticket_1'] . chr(10) . chr(13);
    }

    if (!empty($_POST["ticket_2"]) && isset($_POST["ticket_2"])) {
      $post['user_ticket_2'] = filter_input(INPUT_POST, 'ticket_2', FILTER_SANITIZE_EMAIL);
      $body .= 'Билет №2: ' . $post['user_ticket_2'] . chr(10) . chr(13);
    }

    if (!empty($_POST["birthday"]) && isset($_POST["birthday"])) {
      $post['user_birthday'] = filter_input(INPUT_POST, 'birthday', FILTER_SANITIZE_EMAIL);
      $body .= 'День рождения: ' . $post['user_birthday'] . chr(10) . chr(13);
    }

    if (!empty($_POST["gender"]) && isset($_POST["gender"])) {
      $post['user_gender'] = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_EMAIL);
      $body .= 'Пол: ' . $post['user_gender'] . chr(10) . chr(13);
    }

    if (!empty($_POST["cinema_per_month"]) && isset($_POST["cinema_per_month"])) {
      foreach( $_POST["cinema_per_month"] as $key => $value){ 
        $post['cinema_per_month'] .= $value . ', ';
      }

      $body .= 'Сколько раз в месяц вы ходите в кино: ' . $post['cinema_per_month'] . chr(10) . chr(13);
    }
    
    if (!empty($_POST["cinema_3d"]) && isset($_POST["cinema_3d"])) {
      foreach( $_POST["cinema_3d"] as $key => $value){ 
        $post['cinema_3d'] .= $value . ', ';
      }

      $body .= 'Как часто вы ходите в кино на формат 3D: ' . $post['cinema_3d'] . chr(10) . chr(13);
    }

    if (!empty($_POST["genre"]) && isset($_POST["genre"])) {
      foreach( $_POST["genre"] as $key => $value){ 
        $post['genre'] .= $value . ', ';
      }

      $body .= 'Каким жанрам в 3D вы отдавали предпочтение последние полгода: ' . $post['genre'] . chr(10) . chr(13);
    }
    
    if (!empty($_POST["cinema_3d"]) && isset($_POST["cinema_3d"])) {
      foreach( $_POST["cinema_3d"] as $key => $value){ 
        $post['cinema_3d'] .= $value . ', ';
      }

      $body .= 'Сколько раз в месяц вы ходите в кино: ' . $post['cinema_3d'] . chr(10) . chr(13);
    }  

    if (!empty($_POST["card_loyalty"]) && isset($_POST["card_loyalty"])) {
      foreach( $_POST["card_loyalty"] as $key => $value){ 
        $post['card_loyalty'] = $value;
      }

      $body .= 'Сколько раз в месяц вы ходите в кино: ' . $post['card_loyalty'] . chr(10) . chr(13);
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
      return false;
    } else {
      return true;
    }
  }
}

?>