<?php
/* Include framework Globals */
require 'Core/globalIncludes.php';

/* Include namespaces */
use Core\Main\MainRouter; //Use MainRouter class

/* Start routing */
$router = new MainRouter($_SERVER['REQUEST_URI']); //Create router object
