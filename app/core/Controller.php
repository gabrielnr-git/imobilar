<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Controller trait
 */
Trait Controller
{
    // Require a view file
    public function view($path, $data = []){
        if (!empty($data)) extract($data); 

        $filepath = "../app/views/" . $path . ".view.php";
        if (!file_exists($filepath)) {
            $filepath = "../app/views/404.view.php";
        }
        
        require_once $filepath;
    }
}
