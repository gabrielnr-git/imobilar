<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Logout class
 */
class Logout
{
    use Controller;

    public function index(){
        $ses = new \Core\Session;
        if ($ses->is_logged()) {
            $ses->pop('confirmation'); // Remove the admin confirmation

            // remove the remember me
            $tokens = new \Model\Tokens;
            $tokens->remove($ses->getUser('id_usuario'));
            if (isset($_COOKIE['remember_me'])) setcookie("remember_me",null,-1);

            $ses->logout();
        }
        redirect("login");
    }
}
