<?php
//Пространство імен
namespace Core\Main;

/*******************************************************************************
*                                                                              *
*                               Main view class                                *
*                                                                              *
*******************************************************************************/
class MainView {
  /* Свойства об'єкту */
  protected $route; //Шляхи до контроллера, моделі та виду
  protected $SWP; //SWP глобальні данні

  /*
  * Object create
  */
  public function __construct($route, $SWP) {
    $this->route = $route;
    $this->SWP = $SWP;
  }

  /*
  * Метод рендеру сторінки
  */
  public function render($title,$vars = []) {
    $vars['SWP'] = $this->SWP;
    $vars['SWP']['title'] = $title;
    unset($title);

    extract($vars);
    if (file_exists($this->route['view'].'.php')) {
      ob_start();
      require $this->route['view'].'.php';
      $SWP['content'] = ob_get_clean();
      if (isset($this->route['layout'])) {
        require $this->route['layout'].'.php';
      }
      else {
        $this->errorCode(404,$this->SWP['lang']);
      }
    }
    else {
      $this->errorCode(404,$this->SWP['lang']);
    }
  }

  /*
  * Метод редіректу
  */
  public static function redirect($url) {
    header("Location: ".$url);
    exit;
  }

  /*
  * Метод сторінок помилок
  */
  public static function errorCode($code,$lang) {
    http_response_code($code);
    if (file_exists('app/lang/'.$lang.'/errors/'.$code.'.php')) {
      require 'app/lang/'.$lang.'/errors/'.$code.'.php';
    }
    else {
      echo $code;
    }
    exit;
  }
}
