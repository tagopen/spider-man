<?php
  require_once './mysql/meekrodb.2.3.class.php';
  class ModelClass {
    public $formNamePlace;
    public $formNamePromocode;
    public $error = array();
    public $result = array();

    public function __construct () {
      //DB::$user = 'b18152559_suvvah';
      //DB::$password = '9Z6n3S2x';
      //DB::$dbName = 'b18152559_boris';
      
      DB::$user = 'pimgroup_spider';
      DB::$password = 'spiderman';
      DB::$dbName = 'pimgroup_spider';
      DB::$host = 'localhost'; //defaults to localhost if omitted
      DB::$encoding = 'utf8'; // defaults to latin1 if omitted

      $this->formNamePromocode = 'promo';
      $this->formNamePlace = 'place';
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

    function promocodeIsValid($promocode, $cinema, $status) {
      $results = DB::query("SELECT pr.* FROM promo AS pr LEFT JOIN cinema AS cm ON pr.cinema_id=cm.cinema_id WHERE pr.promo_name=%s AND cm.cinema_name=%s AND pr.online_status=%i", $promocode, $cinema, $status );
      if ($results) {
        foreach ($results as $row) {
          if ($row['promo_status'] === '0') {
            $this->error['promo_error'] = "Такой промокод уже зарегистрирован!";
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
        }
      }
    }
  }

  $obj = new ModelClass();
  $obj->parseGet($_POST);
  $obj->returnQuery();

?>