<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Cadastro class
 */
class Cadastro
{
    use Controller;

    public function index(){
        $data = [];
        $errors = [];
        $req = new \Core\Request;
        $ses = new \Core\Session;
        $ses->pop('alterar'); // For forgot password
        $ses->pop('active'); // For 2FA
        
        // Check if the user is already logged in
        if ($ses->is_logged()) {
            redirect('perfil');
            die();
        }

        // Check for post request
        if ($req->posted() && $req->post('token') == $ses->pop('token')) {
            unset($_POST['token']);
            
            $usuario = new \Model\Usuario;
            $usuario->deleteExpired();
            $usuario->signup($req->post());
            
            $errors = $usuario->getErrors();
        }

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['errors'] = $errors;
        $data['ses'] = $ses;
        $this->view('cadastro',$data);
    }
}
