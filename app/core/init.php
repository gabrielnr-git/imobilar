<?php

if (!defined("ROOTPATH")) die("Access Denied");

// If a class is not found, try to load from models
spl_autoload_register(function($classname){
    $classname = explode('\\',$classname);
    $classname = end($classname);
    require_once "../app/models/" . ucfirst($classname) . ".php";
});


// Load all necessary files
require_once "config.php";
require_once "functions.php";
require_once "Session.php";
require_once "Request.php";
require_once "Pager.php";
require_once "Image.php";
require_once "Mailer.php";
require_once "Database.php";
require_once "Model.php";
require_once "Controller.php";
require_once "App.php";