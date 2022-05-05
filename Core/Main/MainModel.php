<?php
namespace Core\Main;

use Core\lib\Db;

/*******************************************************************************
*                                                                              *
*                               Main model class                               *
*                                                                              *
*******************************************************************************/
class MainModel {
  public $db;

  /*
  * Object start function
  */
  function __construct() {
    $this->db = new Db;
  }
}
