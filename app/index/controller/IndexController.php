<?php
//this namespace
namespace app\index\controller;

//use SWP MainController
use Core\Main\MainController;

/*******************************************************************************
*                                                                              *
*                               index page class                               *
*                                                                              *
*******************************************************************************/
class IndexController extends MainController {
  /*
  * index page Action
  */
  public function IndexAction() {
    $this->view->render('index_page');
  }
}
