<?php

namespace Core;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * App class
 */
class App
{
    private $controller = "Home";
    private $method = "index";

    // Get the URL information
    private function splitURL(){
        $url = $_GET['url'] ?? "home";
        $url = explode('/', trim($url, '/'));
        return $url;
    }
    
    // Load the Controller based on the URL
    public function loadController(){
        $url = $this->splitURL();
    
        // Call the Controller
        $filepath = "../app/controllers/" . ucfirst($url[0]) . ".php";
        $this->controller = ucfirst($url[0]);
        if (!file_exists($filepath)) {
            $filepath = "../app/controllers/_404.php";
            $this->controller = "_404";
        }
        require_once $filepath;

        // Call the method inside the controller
        $controller = new ("\Controller\\" . $this->controller);
        if (!empty($url[1])) {
            if (method_exists($controller, $url[1]) && is_callable([$controller, $url[1]])) {
                $this->method = $url[1];
            }
        }

        unset($url[0],$url[1]);

        call_user_func_array([$controller,$this->method],$url);
    }
}
