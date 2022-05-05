<?php
/* Підключення глобальних частин */
require 'Core/globalIncludes.php';

/* Підключення роутінгу */
use Core\Main\MainRouter;

/* Створення об'єкта роутінгу */
$router = new MainRouter($_SERVER['REQUEST_URI']);
