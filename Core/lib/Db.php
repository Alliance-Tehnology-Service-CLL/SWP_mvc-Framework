<?php
namespace Core\lib;

use PDO;

/*******************************************************************************
*                                                                              *
*                               DataBase class                                 *
*                                                                              *
*******************************************************************************/
class Db {

  protected $db;

  /*
  * Start function
  */
  function __construct() {
    require 'Core/Main/MainConfig.php';
    if (!empty($config['db_conn']['host'])) {
      $this->db = new PDO('mysql:host='.$config['db_conn']['host'].';dbname='.$config['db_conn']['db_name'].'', $config['db_conn']['user'], $config['db_conn']['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
    }
    else {
      $this->db = false;
    }
  }

  /************************************** SQL FUNCTIONS **************************************************************/
  public function SWP_SQL($sql,$type = 'query',$params = []) {
    if ($this->db) {
      if ($type = 'row') {
        return $this->row($sql,$params);
      }
      return $this->query($sql,$params);
    }
    else {
      return 'Connection Error';
    }
  }

  /*************************** SQL PROTECTED FUNCTIONS ******************************/
  /*
  * Standart SQL-query
  */
  protected function query($sql,$params = [])
  {
    if (!empty($params)) {
      $stmt = $this->db->prepare($sql);
      foreach ($params as $key => $val) {
        $stmt->bindValue(':'.$key, $val);
      }
      unset($key);
      unset($val);

      $stmt->execute();
    }
    else {
      $stmt = $this->db->query($sql);
    }
    return $stmt;
  }

  /*
  * Row function
  */
  protected function row($sql,$params = [])
  {
    $result = $this->query($sql,$params);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }
}
