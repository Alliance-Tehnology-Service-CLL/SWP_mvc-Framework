<?php
//this namespace
namespace Core\Main;

/*******************************************************************************
*                                                                              *
*                               Main view class                                *
*                                                                              *
*******************************************************************************/
class MainView {
  /* Object variables */
  public $layout = 'default'; //view layout
  protected $route; //For route from SWP mainontroller
  protected $SWP; //SWP variable

  /*
  * Object create function
  */
  public function __construct($route, $SWP) {
    $this->route = $route; //Route from SWP mainontroller
    $this->SWP = $SWP;
  }

  /*
  * Render function
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
  * Redirect function
  */
  public static function redirect($url) {
    header("Location: ".$url);
    exit;
  }

  /*
  * Page errors function
  */
  public static function errorCode($code,$lang) {
    http_response_code($code);
    if (file_exists('app/'.$lang.'/errors/'.$code.'.php')) {
      require 'app/'.$lang.'/errors/'.$code.'.php';
    }
    else {
      echo $code;
    }
    exit;
  }
}
