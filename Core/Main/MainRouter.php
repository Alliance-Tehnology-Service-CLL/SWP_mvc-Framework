<?php
/* This namespace */
namespace Core\Main;

use Core\Main\MainView;
/*******************************************************************************
*                                                                              *
*                               Main routing class                             *
*                                                                              *
*******************************************************************************/
class MainRouter {
  /* Object variables */
  public $lang; //Routs array
  protected $url; //User called link
  protected $get_querys; //GET querys
  protected $routs; //Routs array

  /*
  * Object create function
  */
  function __construct($url) {
    $this->url = trim($url, '/'); //Set user colled link
    if (empty($this->url)) { //If call index page
      $this->url = 'index'; //Index page route
    }
    $this->run(); //Start finction call
  }

  /*
  * Start function
  */
  public function run() {
    $this->get_querys = $this->check_get_query(); //Call check GET querys function
    $this->lang = $this->check_user_language(); //Take user language
    if ($this->routs = $this->check_routes()) {
      $controller = $this->routs['controller'];
      $controller = new $controller($this->routs,$this->lang,$this->get_querys);
      $action = $this->routs['action'];
      $controller->$action();
      unset($action);
    }
    else {
      MainView::errorCode(404,$this->lang);
    }
  }

  /*************************** PROTECTED FUNCTIONS ******************************/
  /*
  * Check GET query funnction
  */
  protected function check_get_query() {
    $get = explode("?", $this->url); //Fined GET querys
    if (!empty($get[1])) { //If fined GET querys
      $get = explode("&", $get[1]); //Return array of GET querys
      foreach ($get as $value) { //Format GET array
        $data = explode("=",$value);
        $result[$data[0]] = $data[1]; //Set result array like $result[GET_name] = GET value
      }
      return $result; //Return GET array
    }
    return NULL; //Return NULL
  }

  /*
  * Check language funnction
  */
  protected function check_user_language() {
    //Language Set
    if (isset($this->get_querys['lang'])) {
      if (is_dir('app/leng/'.$this->get_querys['lang'])) {
        $lang = $this->get_querys['lang']; //Set language
        setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
        MainView::redirect("/".$lang."/");
      }
    }

    //Language Check
    if (!isset($_COOKIE['lang'])) {
      if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
        preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]), $matches);
        $langs = array_combine($matches[1], $matches[2]);
        foreach($langs as $n => $v) {
          $langs[$n] = $v ? $v : 1;
        }
        arsort($langs);
        $lang = key($langs);
        $lang = substr($lang, 0, 2);
        if (!is_dir('app/lang/'.$lang)) {
          $lang = 'en'; //Default language
          setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
        }
        else {
          setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
        }
      }
      else {
        $lang = 'en'; //Default language
        setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
      }
    }
    else {
      if ($this->lang != $_COOKIE['lang']) {
        $lang = $_COOKIE['lang'];
      }
    }

    $lang_url = explode("/",$this->url);
    if ($lang_url[0] !== $lang) {
      unset($lang_url[0]);
      $lang_url = implode("/",$lang_url);
      MainView::redirect("/".$lang."/".$lang_url);
    }

    return $lang;
  }

  /*
  * Check routes function
  */
  protected function check_routes() {
    $data = explode("?", $this->url);
    $data = explode("/", $data[0]); //Take names
    $lang = $data[0]; //Take page folder
    if (isset($data[1])) {
      $folder = $data[1]; //Take page folder
    }
    else {
      $folder = 'index';
    }

    $controller = $data[array_key_last($data)]; //Take controller name
    if ($controller === $lang) {
      $controller = 'index';
    }
    $route = ''; //Set EMPTY route variable
    foreach ($data as $value) {
      if ($value !== $data[0] AND $value !== $data[array_key_last($data)]) {
        $route .= '/'.$value;
      }
    }
    unset($data);
    unset($value);

    $routes = [
      'controller' => 'app/'.$folder.'/controller'.$route.'/'.ucfirst($controller).'Controller',
      'action' => ucfirst($controller).'Action',
      'view' => 'app/lang/'.$lang.'/view'.$route.'/'.$controller,
      'model' => 'app/'.$folder.'/model'.$route.'/'.ucfirst($controller).'Model',
    ];
    if (file_exists('app/lang/'.$lang.'/layout/'.$controller.'.php')) {
      $routes['layout'] = 'app/lang/'.$lang.'/layout/'.$controller;
    }
    elseif (file_exists('app/lang/'.$lang.'/layout/default.php')) {
      $routes['layout'] = 'app/lang/'.$lang.'/layout/default';
    }
    else {
      $routes['layout'] = 'app/lang/layout/default';
    }
    unset($route);
    unset($folder);
    unset($controller);

    $routes['controller'] = str_replace("/","\\",$routes['controller']);
    $routes['model'] = str_replace("/","\\",$routes['model']);

    if (class_exists($routes['controller'])) {
      if (method_exists($routes['controller'], $routes['action'])) {
        if (class_exists($routes['model'])) {
          return $routes;
        }
      }
    }

    return false;
  }
}
