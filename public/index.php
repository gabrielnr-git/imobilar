<?php

// Check the PHP version
if (phpversion() < "8.0") die("Your php version must be 8.0 or higher to run this app. Your current version is ".phpversion());

// Path to this file
define('ROOTPATH', true);

require_once "../app/core/init.php";
session_start();
regenerate_id();

$app = new \Core\App;
$app->loadController();