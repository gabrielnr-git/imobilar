<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Home class
 */
class Home
{
    use Controller;

    public function index(){
        $data = [];
        $this->view('home',$data);
    }
}
