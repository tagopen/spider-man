<?php
  session_start();
  require_once './mysql/meekrodb.2.3.class.php';
  require_once './mail/mail.php';
  class ModelClass {
    public $formNamePlace;
    public $formNamePromocode;
    public $error = array();
    public $result = array();
    public $salt = '';

    public function __construct () {
      //DB::$user = 'b18152559_suvvah';
      //DB::$password = '9Z6n3S2x';
      //DB::$dbName = 'b18152559_boris';
      
      //DB::$user = 'pimgroup_spider';
      //DB::$password = 'spiderman';
      //DB::$dbName = 'pimgroup_spider';
      DB::$user = 'root';
      DB::$password = '';
      DB::$dbName = 'boris';
      DB::$host = 'localhost'; //defaults to localhost if omitted
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

    function setError ($text) {

    }

    function promocodeIsValid($promocode, $cinema, $purchase) {
      $results = DB::query("SELECT pr.* FROM promo AS pr LEFT JOIN cinema AS cm ON pr.cinema_id=cm.cinema_id WHERE pr.promo_name=%s AND cm.cinema_name=%s AND pr.purchase_id=%i", $promocode, $cinema, $purchase );
      if ($results) {
        foreach ($results as $row) {
          if ($row['promo_status'] === '0') {
            $this->error['promo_error'] = "Такой промокод уже зарегистрирован!";
          } elseif ($row['promo_status'] === '1') {
            DB::update('promo', array(
              'promo_status' => '0'
            ), "promo_name=%s", $promocode);
            $this->result['promo_status'] = "Промокод успешно активирован!";
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
        'date_added' => date('Y-m-d H:i:s')
      ));

      if (MailClass::sendMail()) {
        $this->result['promo_status'] = "Сообщение успешно отправлено!";
      } else {
        $this->result['promo_error'] = "Сообщение не отправлено!";
      }

      if(isset($_SESSION['promo'])) {
        unset($_SESSION['promo']);
      }
    }

    function returnQuery() {
      if ($this->error) {
        echo json_encode(array("result" => $this->error), JSON_FORCE_OBJECT);
        return false;
        //echo json_encode(array("result" => "Такого кода не существует!"), JSON_FORCE_OBJECT);
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
          } elseif ($_SESSION['promo'] == sha1($this->salt . $post['promo'])) {//Never did this
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
            $this->error['promo_error'] = "Произошла ошибка. Обратитесь к администрации сайта";
          }
        }

      }
    }
  }

  $obj = new ModelClass();
  $obj->parseGet($_POST);
  $obj->returnQuery();

?>