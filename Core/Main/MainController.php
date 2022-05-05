<?php
//this namespace
namespace Core\Main;

use Core\Main\MainView;
/*******************************************************************************
*                                                                              *
*                               Main controller                                *
*                                                                              *
*******************************************************************************/
abstract class MainController {
  public $model;
  public $view;
  protected $routs; //Routs array
  protected $SWP; //SWP array
  protected $get_querys; //GET querys array

  //Start function
  function __construct($routs,$lang,$get_querys) {
    session_start();//start session
    $this->routs = $routs; //Routes
    $this->get_querys = $get_querys; //GET querys
    $this->SWP = [ 'lang' => $lang, ]; //SWP global parameters
    $this->view = new MainView($routs,$this->SWP); //Start view
    $this->model = $this->loadModel($this->routs['model']);
  }

  protected function loadModel($model) {
    if (class_exists($this->routs['model'])) {
      return new $this->routs['model'];
    }
  }
}
