<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Perfil class
 */
class Perfil
{
    use Controller;

    public function index(){
        $data = [];
        $ses = new \Core\Session;

        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        $data['token'] = $ses->get('token');
        $data['ses'] = $ses;
        $this->view('perfil',$data);
    }

    // Display a user
    public function user($id = 0)
    {
        $data = [];
        $ses = new \Core\Session;

        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        // If it is the actual user redirect to it's profile page
        if ($id == 0 || $id == $ses->getUser('id_usuario')) {
            redirect('perfil');
            die();
        }

        // Displaying the user
        $data['row'] = false;

        $usuario = new \Model\Usuario;
        $row = $usuario->select("*",['id_usuario'=>$id]);
        if ($row) {
            unset($row['senha']);
    
            $data['row'] = $row;
        }
        $data['ses'] = $ses;
        $this->view('perfil_user',$data);
    }

    // Edit user page
    public function edit()
    {
        $data = [];
        $errors = [];
        $req = new \Core\Request;
        $ses = new \Core\Session;

        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }
        
        // Check a post request and CSRF token
        if ($req->posted() && $req->post('token') == $ses->pop('token')) {
            unset($_POST['token']);
            
            $usuario = new \Model\Usuario; // Get the user model

            // Check if a image as uploaded and no errors
            if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] == 0) $_POST['pfp'] = $_FILES['pfp'];
            
            // Check if the data is the same
            foreach ($_POST as $column => $value) {
                if ($value == $ses->getUser($column)) unset($_POST[$column]);
            }

            if ($req->post() === '') {
                redirect('perfil');
                die();
            }

            // Validate the data to be updated
            if ($usuario->validate($req->post(),'update')){
                // If phone number is given, only take the raw number
                if ($req->post('telefone') !== '') 
                    $_POST['telefone'] = preg_replace("/[^0-9]/","",$_POST['telefone']);

                // If pfp is given, save it
                if ($req->post('pfp') !== '') {
                    $image = new \Core\Image; // Get the Image manipulation class
    
                    // Check if the user already have a pfp and delete it
                    if (file_exists($ses->getUser('pfp'))) unlink($ses->getUser('pfp'));
    
                    // Upload and resize the pfp
                    $_POST['pfp'] = $image->upload("assets/images/uploads/pfp/",$req->post('pfp'));
                    $image->resize($_POST['pfp'],256);
                }
                
                // Update the user data
                $usuario->update($req->post(),['id_usuario'=>$ses->getUser('id_usuario')]);

                // Refresh the auth data
                $refresh = $usuario->select("*",['id_usuario'=>$ses->getUser('id_usuario')]);
                if ($refresh) {
                    // Format the raw phone number
                    $refresh['telefone'] = formatPhone($refresh['telefone']);

                    unset($refresh['senha']);

                    $ses->regenerate();
                    $ses->auth($refresh);

                    addNotification("Perfil Atualizado","Suas informações de perfil foram atualizadas!",$refresh['id_usuario'],"/perfil");

                    message("Alterações salvas!");
                    redirect('perfil');
                    die();
                } else {
                    $_POST['telefone'] = formatPhone($_POST['telefone']);
                    $errors['update'] = "Algo deu errado. Tente novamente";
                }
                
            }

            $errors = $usuario->getErrors();
        
        }

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token
        
        $data['ses'] = $ses;
        $data['errors'] = $errors;
        $this->view('perfil_edit',$data);
    }

    // Delete the current user
    public function delete()
    {
        $ses = new \Core\Session;
        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            return false;
            die();
        }
        
        $req = new \Core\Request;

        // Check a post request and CSRF token
        if (!$req->posted() || $req->post('token') !== $ses->pop('token')) {
            redirect("perfil");
            return false;
            die();
        }

        $usuario = new \Model\Usuario;

        $id = $ses->getUser('id_usuario'); // Get the current user id

        // Delete the user if success return true, if not return false (for ajax)
        if ($usuario->delete(['id_usuario'=>$id])){
            unlink($ses->getUser('pfp')); // also delete the image related to the user
            $ses->logout();
            message("Conta deletada com sucesso!");
            echo true;
            die();
        } else {
            echo false;
            die();
        }
    }

    public function reset()
    {
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $ses->pop('state');

        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        // false = confirm password, true = change password
        $data['state'] = false;
        $req = new \Core\Request;

        // Check a post request and CSRF token
        if ($req->posted() && $req->post('token') == $ses->pop('token')) {
            $usuario = new \Model\Usuario;
            
            // Get the current user password
            $pwd = $usuario->select("senha",['id_usuario'=>$ses->getUser('id_usuario')]);
        
            // Check the 2 new passwords are the same
            if (password_verify($req->post('senha'),$pwd['senha'])) {
                $ses->set('state',bin2hex(random_bytes(32))); // Random string to be used as a "complete" state
                redirect('perfil/change?state=' . $ses->get('state')); // Redirect to change password
            } else {
                $errors['senha'] = "Senha Incorreta";
            }
        }

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['ses'] = $ses;
        $data['errors'] = $errors;
        $this->view("perfil_reset_pwd",$data);
    }

    public function change()
    {
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $req = new \Core\Request;

        // Check the if the previously state is completed
        if ($req->get('state') != $ses->get('state')){
            redirect('perfil/reset');
            die();
        }

        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        $data['state'] = true;
        
        // Check for post request
        if ($req->posted() || $req->post('token') == $ses->pop('token')) {
            // Password confirmation
            if ($req->post('senha') !== $req->post('confirmacao_senha')) {
                $errors['form'] = "As senhas não são iguais";
            }
            $usuario = new \Model\Usuario;
            $usuario->validate($req->post(),'update'); // Validate the data
            
            $errors = array_merge($errors,$usuario->getErrors());

            if (empty($errors)) {
                
                $ses->pop('state');

                $_POST['senha'] = password_hash($req->post('senha'),PASSWORD_BCRYPT,['cost'=>12]);
                
                if ($usuario->update($req->post(),['id_usuario'=>$ses->getUser('id_usuario')])){
                    addNotification("Senha alterada","Atenção, sua senha foi alterada!",$ses->getUser('id_usuario'));
                    $ses->logout(); 
                    message("Senha alterada com sucesso! Por favor, faça login novamente");
                    redirect('login');
                    die();
                }
                
                $errors['form'] = "Algo deu errado! Tente novamente.";
            }
        }
        
        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['ses'] = $ses;
        $data['errors'] = $errors;
        $this->view("perfil_reset_pwd",$data);
    }
}
