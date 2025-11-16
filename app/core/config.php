<?php

if (!defined("ROOTPATH")) die("Access Denied");

//Timezone config
ini_set("date.timezone","America/Sao_Paulo");

//Session config
ini_set("session.use_only_cookies",1);
ini_set("session.use_strict_mode",1);
session_set_cookie_params([
    'path' => '/',
    'secure' => true,
    'httponly' => true
]);

// Host Config
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    session_set_cookie_params(['domain' => 'localhost']);

    define("ROOT","http://localhost/Imobilar/public");
    define('DBHOST', 'localhost');
    define('DBNAME', 'imobilar');
    define('DBUSER', 'root');
    define('DBPASS', '');
    
} else {
    session_set_cookie_params(['domain' => '']);

    define("ROOT","");
    define('DBHOST', '');
    define('DBNAME', '');
    define('DBUSER', '');
    define('DBPASS', '');
}

//SMTP Config
define("SMTPHOST","");
define("SMTPUSERNAME","");
define("SMTPPASSWORD","");
define("SMTPCONNECTION","ssl");
define("SMTPPORT",465);

// Debug mode: true or false
define("DEBUG", false);
DEBUG ? ini_set("display_errors",1) : ini_set("display_errors",0);