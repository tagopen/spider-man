<?php
  session_start();

  if (is_file('./mysql/meekrodb.2.3.class.php')) {
    require_once("./mysql/meekrodb.2.3.class.php");
  }
  
  if (is_file('./mail/lib/class.phpmailer.php')) {
    require_once("./mail/lib/class.phpmailer.php");
  }
  
  if (is_file('./mail/lib/class.smtp.php')) {
    require_once("./mail/lib/class.smtp.php");
  }

  class ModelClass {
    public $formNamePlace;
    public $formNamePromocode;
    public $error = array();
    public $result = array();
    public $salt = '';

    public function __construct () {
      
      //DB::$user = 'pimgroup_spider';
      //DB::$password = 'spiderman';
      //DB::$dbName = 'pimgroup_spider';

      DB::$user = 'u669653623_spm';
      DB::$password = 'bYZsd4FO5WUl';
      DB::$dbName = 'u669653623_spm';
      DB::$host = 'mysql.hostinger.co.uk'; //defaults to localhost if omitted

      //DB::$user = 'root';
      //DB::$password = '';
      //DB::$dbName = 'boris';

      //DB::$host = 'localhost'; //defaults to localhost if omitted
      DB::$encoding = 'utf8'; // defaults to latin1 if omitted

      $this->formNamePromocode = 'promo';
      $this->formNamePlace = 'place';
      $this->formNameRegistration = 'registration';
      $this->salt = 'Mm3#kK1f9%9d0V';
    }

    function getCities() {
      $results = DB::query("SELECT DISTINCT city FROM place");
      if ($results) {
        $this->result['place_city'] = $results;
      } else {
        $this->error['place_city'] = "Нет данных по городам";
      }
    }

    function getCinemas($city) {
      $results = DB::query("SELECT cm.cinema_name, pl.link FROM cinema AS cm LEFT JOIN place AS pl ON pl.cinema_id=cm.cinema_id WHERE pl.city = %s", $city);
      if ($results) {
        $this->result['place_cinema'] = $results;
      } else {
        $this->error['place_cinema'] = "Нет данных по кинотеатрам";
      }
    }


    function promocodeIsValid($promocode, $cinema, $purchase) {
      $results = DB::query("SELECT pr.* FROM promo AS pr LEFT JOIN cinema AS cm ON pr.cinema_id=cm.cinema_id WHERE pr.promo_name=%s AND cm.cinema_name=%s AND pr.purchase_id=%i", $promocode, $cinema, $purchase );
      if ($results) {
        foreach ($results as $row) {
          if ($row['promo_status'] === '0') {
            $this->error['promo_error'] = "Такой промокод уже зарегистрирован!";
          } elseif ($row['promo_status'] === '1') {
            $this->result['promo_status'] = "Промокод существует!";
            $_SESSION['promo'] = sha1($this->salt . $promocode);
          }
        }
      } else {
        $this->error['promo_error'] = "Такого кода не существует!";
      }
    }

    function setOrder($data) {

      DB::insert('ordered', array(
        'cinema' => $data['cinema'],
        'purchase' => $data['purchase'],
        'promo' => $data['promo'],
        'ticket_1' => $data['ticket_1'],
        'ticket_2' => $data['ticket_2'],
        'email' => $data['email'],
        'birthday' => $data['birthday'],
        'gender' => $data['gender'],
        'cinema_per_month' => ($data['cinema_per_month']) ? implode(", ", $data['cinema_per_month']) : '',
        'cinema_3d' => ($data['cinema_3d']) ? implode(", ", $data['cinema_3d']) : '',
        'genre' => (!empty($data['genre'])) ? $data['genre'] : ' ',
        'card_loyalty' => ($data['card_loyalty']) ? implode(", ", $data['card_loyalty']) : '',
        'date_added' => DB::sqleval("NOW()")
      ));

      DB::update('promo', array(
        'promo_status' => '0',
        'date_modified' => DB::sqleval("NOW()")
      ), "promo_name=%s", $data['promo']);

      ModelClass::sendMail();

      if(isset($_SESSION['promo'])) {
        unset($_SESSION['promo']);
      }
    }

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

      /*if ( (!empty($_POST["form"])) && (isset($_POST["form"])) ) {
        foreach( $_POST["form"] as $key => $value) { 
          $post['user_form'] = $key;
          $body .= 'Форма: ' . $post['user_form'] . chr(10) . chr(13);
        }
      }*/

      if ( (!empty($_POST["form"])) && (isset($_POST["form"])) ) {
        $post['user_form'] = filter_input(INPUT_POST, 'form', FILTER_SANITIZE_STRING);
        $body .= 'Форма: ' . $post['user_form'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["email"])) && (isset($_POST["email"])) ) {
        $post['user_email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $body .= 'Email: ' . $post['user_email'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["cinema"])) && (isset($_POST["cinema"])) ) {
        $post['user_cinema'] = filter_input(INPUT_POST, 'cinema', FILTER_SANITIZE_STRING);
        $body .= 'Кинотеатр: ' . $post['user_cinema'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["purchase"])) && (isset($_POST["purchase"])) ) {
        $post['user_purchase'] = filter_input(INPUT_POST, 'purchase', FILTER_SANITIZE_STRING);
        $body .= 'Свособ оплаты: ' . $post['user_purchase'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["promo"])) && (isset($_POST["promo"])) ) {
        $post['user_promo'] = filter_input(INPUT_POST, 'promo', FILTER_SANITIZE_STRING);
        $body .= 'Промо код: ' . $post['user_promo'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["ticket_1"])) && (isset($_POST["ticket_1"])) ) {
        $post['user_ticket_1'] = filter_input(INPUT_POST, 'ticket_1', FILTER_SANITIZE_STRING);
        $body .= 'Билет №1: ' . $post['user_ticket_1'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["ticket_2"])) && (isset($_POST["ticket_2"])) ) {
        $post['user_ticket_2'] = filter_input(INPUT_POST, 'ticket_2', FILTER_SANITIZE_STRING);
        $body .= 'Билет №2: ' . $post['user_ticket_2'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["birthday"])) && (isset($_POST["birthday"])) ) {
        $post['user_birthday'] = filter_input(INPUT_POST, 'birthday', FILTER_SANITIZE_STRING);
        $body .= 'День рождения: ' . $post['user_birthday'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["gender"])) && (isset($_POST["gender"])) ) {
        $post['user_gender'] = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        $body .= 'Пол: ' . $post['user_gender'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["cinema_per_month"])) && (isset($_POST["cinema_per_month"])) ) {
        foreach( $_POST["cinema_per_month"] as $key => $value) { 
          $post['cinema_per_month'] .= $value . ', ';
        }

        $body .= 'Сколько раз в месяц вы ходите в кино: ' . $post['cinema_per_month'] . chr(10) . chr(13);
      }
      
      if ( (!empty($_POST["cinema_3d"])) && (isset($_POST["cinema_3d"])) ) {
        foreach( $_POST["cinema_3d"] as $key => $value) { 
          $post['cinema_3d'] .= $value . ', ';
        }

        $body .= 'Как часто вы ходите в кино на формат 3D: ' . $post['cinema_3d'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["genre"])) && (isset($_POST["genre"])) ) {
        foreach( $_POST["genre"] as $key => $value) { 
          $post['genre'] .= $value . ', ';
        }

        $body .= 'Каким жанрам в 3D вы отдавали предпочтение последние полгода: ' . $post['genre'] . chr(10) . chr(13);
      }

      if ( (!empty($_POST["card_loyalty"])) && (isset($_POST["card_loyalty"])) ) {
        foreach( $_POST["card_loyalty"] as $key => $value) { 
          $post['card_loyalty'] = $value;
        }

        $body .= 'Есть ли у вас карта лояльности какой-нибудь киносети: ' . $post['card_loyalty'] . chr(10) . chr(13);
      }

      $body .= chr(10) . chr(13) . "С уважением," . chr(10) . chr(13) . "разработчики сайта " . $post['host_referer'];

      $mail = new PHPMailer();
      $mail->CharSet      = 'UTF-8';
      //$mail->IsSendmail();

      $from = 'order@spiderman.reald3d.ru';
      $to = "avdeevkk@gmail.com";
      $mail->SetFrom($from, HOST_NAME);
      $mail->AddAddress($to);
      $mail->isHTML(false);
      $mail->Subject      = "Новая заявка 'Человек паук, возвращение домой'";
      $mail->Body         = $body;

      if(!$mail->send()) {
        $this->result['promo_error'] = "Сообщение не отправлено!" . $mail->ErrorInfo;
      } else {
        $this->result['promo_status'] = "Сообщение успешно отправлено!";
      }
    }

    function returnQuery() {
      if ($this->error) {
        echo json_encode(array("result" => $this->error), JSON_FORCE_OBJECT);
        return false;
      } elseif ($this->result) {
        echo json_encode(array("result" => $this->result), JSON_FORCE_OBJECT);
        return true;
      }
    }

    function parseGet($post) {
      $formName = $post['formname'];

      if (isset($formName)) {
        if ( strcasecmp ($formName, $this->formNamePlace) == 0 ) {
          if (!isset($post['city'])) {
            ModelClass::getCities();
          } elseif ( (isset($post['city'])) && (!isset($post['cinema'])) ) {
            ModelClass::getCinemas($post['city']);
          }
        } elseif ( strcasecmp ($formName, $this->formNamePromocode) == 0 ) {
          if (!isset($post['promo'])) {
            $this->error['promo_error'] = "Введите промокод";
          } elseif (!isset($post['cinema'])) {
            $this->error['promo_error'] = "Выберите кинотеатр";
          } elseif (!isset($post['status'])) {
            $this->error['promo_error'] = "Не выбрано, как совершалась покупка";
          } else {
            ModelClass::promocodeIsValid($post['promo'], $post['cinema'], $post['status']);
          }
        } elseif ( strcasecmp ($formName, $this->formNameRegistration) == 0 ) {
          if (!isset($post['cinema'])) {
            $this->error['promo_error'] = "Выберите кинотеатр";
          } elseif (!isset($post['purchase'])) {
            $this->error['promo_error'] = "Выберите способ оплаты";
          } elseif (!isset($post['promo'])) {
            $this->error['promo_error'] = "Не корректный промокод";
          } elseif (!isset($post['ticket_1'])) {
            $this->error['promo_error'] = "Введите билет №1";
          } elseif (!isset($post['ticket_2'])) {
            $this->error['promo_error'] = "Введите билет №2";
          } elseif (!isset($post['email'])) {
            $this->error['promo_error'] = "Введите email";
          } elseif (!isset($post['birthday'])) {
            $this->error['promo_error'] = "Введите дату рождения";
          } elseif (!isset($post['gender'])) {
            $this->error['promo_error'] = "Выберите ваш пол";
          } elseif (!isset($post['cinema_per_month'])) {
            $this->error['promo_error'] = "Выберите, сколько раз в месяц вы ходите в кино";
          } elseif (!isset($post['cinema_3d'])) {
            $this->error['promo_error'] = "Выберите, как часто вы ходите в кино на формат 3D";
          } elseif (($_SESSION['promo'] == sha1($this->salt . $post['promo'])) || (strcasecmp ($post['cinema'], "Национальная сеть кинотеатров СИНЕМА ПАРК") == 0)) {//Never did this
            $data = array(
              'cinema' => $post['cinema'],
              'purchase' => $post['purchase'],
              'promo' => $post['promo'],
              'ticket_1' => $post['ticket_1'],
              'ticket_2' => $post['ticket_2'],
              'email' => $post['email'],
              'birthday' => $post['birthday'],
              'gender' => $post['gender'],
              'cinema_per_month' => $post['cinema_per_month'],
              'cinema_3d' => $post['cinema_3d'],
              'genre' => $post['genre'],
              'card_loyalty' => $post['card_loyalty']
            );

            ModelClass::setOrder($data);
          } else {
            $this->error['promo_error'] = "Воспользуйтесь кнопкой \"Проверить промокод\"";
          }
        }

      }
    }
  }

  $obj = new ModelClass();
  $obj->parseGet($_POST);
  $obj->returnQuery();

?>