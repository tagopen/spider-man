<?php
  require_once './mysql/meekrodb.2.3.class.php';
  class ModelClass {
    public $formNamePlace;
    public $formNamePromocode;
    public $error = array();
    public $result = array();

    public function __construct () {
      DB::$user = 'b18152559_suvvah';
      DB::$password = '9Z6n3S2x';
      DB::$dbName = 'b18152559_boris';
      DB::$host = 'localhost'; //defaults to localhost if omitted
      DB::$encoding = 'utf8'; // defaults to latin1 if omitted

      $this->formNamePromocode = 'promo';
      $this->formNamePlace = 'place';
    }

    function getCities() {
      $results = DB::query("SELECT city FROM place");
      if ($results) {
        $this->result['place_city'] = $results;
      } else {
        $this->error['place_city'] = "Нет данных по городам";
      }
    }

    function getCinemas($city) {
      $results = DB::query("SELECT cm.cinema_name FROM cinema AS cm LEFT JOIN place AS pl ON pl.cinema_id=cm.cinema_id WHERE pl.city = %s", $city);
      if ($results) {
        $this->result['place_cinema'] = $results;
      } else {
        $this->error['place_cinema'] = "Нет данных по кинотеатрам";
      }
    }

    function getLink($city, $cinema) {
      $results = DB::query("SELECT pl.link FROM place AS pl LEFT JOIN cinema AS cm ON pl.cinema_id=cm.cinema_id WHERE pl.city=%s AND cm.cinema_name=%s", $city, $cinema);
      if ($results) {
        $this->result['place_link'] = $results;
      } else {
        $this->error['place_link'] = "Ссылка не найдена";
      }
    }

    function promocodeIsValid($promocode, $cinema, $status) {
      $results = DB::query("SELECT pr.* FROM promo AS pr LEFT JOIN cinema AS cm ON pr.cinema_id=cm.cinema_id WHERE pr.promo_name=%s AND cm.cinema_name=%s AND pr.online_status=%i", $promocode, $cinema, $status );
      if ($results) {
        var_dump($results);
        foreach ($results as $row) {
          if ($row['promo_status'] === '0') {
            $this->error['promo_status'] = "Такой промокод уже зарегистрирован!";
          } elseif ($row['promo_status'] === '1') {
            DB::update('promo', array(
              'promo_status' => '0'
            ), "promo_name=%s", $promocode);
            $this->result['promo_status'] = "Промокод успешно активирован!";
          }
        }
      } else {
        $this->error['promo_error'] = "Такого кода не существует!";
      }
    }

    function returnQuery() {
      echo "<pre>";
      if ($this->error) {
        print_r($this->error);

        //echo json_encode(array("result" => "Такого кода не существует!"), JSON_FORCE_OBJECT);
      } elseif ($this->result) {
        print_r($this->result);
      }
      echo "</pre>";
    }

    function parseGet($post) {
      $formName = $post['formname'];

      if (isset($formName)) {
        if ( strcasecmp ($formName, $this->formNamePlace) == 0 ) {
          if (!isset($post['city'])) {
            ModelClass::getCities();
          } elseif ( (isset($post['city'])) && (!isset($post['cinema'])) ) {
            ModelClass::getCinemas($post['city']);
          } elseif ( (isset($post['city'])) && (isset($post['cinema'])) ) {
            ModelClass::getLink($post['city'], $post['cinema']);
          }
        } elseif ( strcasecmp ($formName, $this->formNamePromocode) == 0 ) {
          if (!isset($post['promo'])) {
            $this->error['promo_code'] = "Введите промокод";
          } elseif (!isset($post['cinema'])) {
            $this->error['promo_cinema'] = "Выберите кинотеатр";
          } elseif (!isset($post['status'])) {
            $this->error['promo_status'] = "Не выбрано, как совершалась покупка";
          } else {
            ModelClass::promocodeIsValid($post['promo'], $post['cinema'], $post['status']);
          }
        }
      }
    }
  }

  $obj = new ModelClass();
  $obj->parseGet($_GET);
  $obj->returnQuery();

?>