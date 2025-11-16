<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * _404 class
 */
class _404
{
    use Controller;

    public function index(){
        $data = [];
        $this->view('404',$data);
    }
}
