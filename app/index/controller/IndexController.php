<?php
//Пространство імен
namespace app\index\controller;

//Підключення SWP MainController
use Core\Main\MainController;

/*******************************************************************************
*                                                                              *
*                               Контроллер Index                               *
*                                                                              *
*******************************************************************************/
class IndexController extends MainController {
  /*
  * Головна функцыя сторынки Index
  */
  public function IndexAction() {
    $this->view->render('index_page');
  }
}
