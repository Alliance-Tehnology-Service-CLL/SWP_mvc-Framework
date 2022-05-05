<?php
if ($config['debug']['errors']) {
  //Allow php-errors
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}
else {
  //Disallow php-errors
  ini_set('display_errors', 0);
  error_reporting(0);
}

if ($config['debug']['tools']) {
  //Output something and stop
  function debug($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    exit;
  }

  //Output something
  function dump($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
  }
}
