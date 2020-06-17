<?php

use Slim\Factory\AppFactory;
use Slim\Views\Twig;

require_once "vendor/autoload.php";
require_once "src/db.php";
require_once "src/autentica.php";
require_once "src/alerta.php";

session_start();

$app = AppFactory::create();
$view = Twig::create("views", ["cache"], false);