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
  public $lang; //Мова користувача
  protected $url; //Шлях сторінки
  protected $get_querys; //GET-запити
  protected $routs; //Массив зі шляхами до файлів сторінки

  /*
  * Object create function
  */
  function __construct($url) {
    $this->url = trim($url, '/'); //Визначення сторінки до якої звернувся користувач
    if (empty($this->url)) { //Якщо користувач звернувся по імені сайту
      $this->url = 'index'; //Встановлення сторінки по-замовчуванню
    }
    $this->run(); //Запуск пошуку шляхів
  }

  /*
  * Запуск пошуку шляхів
  */
  public function run() {
    $this->get_querys = $this->check_get_query(); //Запит пошуку GET-запитів
    $this->lang = $this->check_user_language(); //Визначення мови користувача
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
  * Перевірка та запис GET-запитів
  */
  protected function check_get_query() {
    $get = explode("?", $this->url); //Пошук запитів
    if (!empty($get[1])) { //Якщо запит був
      $get = explode("&", $get[1]); //Отримуєм запити
      foreach ($get as $value) { //Формотуєм запити у массив
        $data = explode("=",$value);
        $result[$data[0]] = $data[1]; //Вигляд массиву запитів: $result[GET_name] = GET value
      }
      return $result; //Повертаєм результат
    }
    return NULL; //Повертаєм NULL
  }

  /*
  * Визначення мови користувача
  */
  protected function check_user_language() {
    //Встановлення мови
    if (isset($this->get_querys['lang'])) {
      if (is_dir('app/lang/'.$this->get_querys['lang'])) {
        $route = explode("?",$this->url);
        $route = explode("/",$route[0]);
        unset($route[0]);
        $route = implode("/",$route);
        $lang = $this->get_querys['lang']; //Set language
        setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
        MainView::redirect("/".$lang."/");
      }
    }

    //Візначення мови
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
          $lang = 'en'; //Мова по-замовчуванню
          setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
        }
        else {
          setcookie("lang", $lang, time() + (365 * 24 * 60 * 60), "/");
        }
      }
      else {
        $lang = 'en'; //Мова по-замовчуванню
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
  * Перевірка шляхів
  */
  protected function check_routes() {
    $data = explode("?", $this->url);
    $data = explode("/", $data[0]); //Візначаємо імена
    $lang = $data[0]; //Мова запиту
    if (isset($data[1])) {
      $folder = $data[1]; //Папки запиту
    }
    else {
      $folder = 'index'; //Папка по-замовчуванню
    }

    $controller = $data[array_key_last($data)]; //Візначаємо ім'я контроллера
    if ($controller === $lang) {
      $controller = 'index'; //Ім'я контроллера по-замовчуванню
    }
    $route = ''; //Якщо шлях пустий
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
    elseif (file_exists('app/lang/'.$lang.'/layout'.$route.'/'.$controller.'.php')) {
      $routes['layout'] = 'app/lang/'.$lang.'/layout'.$route.'/'.$controller;
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
