<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Login class
 */
class Login
{
    use Controller;

    // Normal login
    public function index(){
        $data = [];
        $errors = [];
        $req = new \Core\Request;
        $ses = new \Core\Session;
        $ses->pop('alterar'); // For forgot password
        $ses->pop('active'); // For 2FA

        // Check if user is logged in
        if ($ses->is_logged()) {
            redirect('perfil');
            die();
        }

        // Check the post request and the CSRF token
        if ($req->posted() && $req->post('token') == $ses->pop('token')) {
            unset($_POST['token']);
            
            // Login the user
            $usuario = new \Model\Usuario;
            $usuario->deleteExpired();
            $usuario->login($req->post());
            $errors = $usuario->getErrors();
        }
        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['errors'] = $errors;
        $data['ses'] = $ses;
        $this->view('login',$data);
    }

    // Function to restore the password
    public function recuperar()
    {
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $req = new \Core\Request;

        if ($req->posted() && $req->post('token') === $ses->pop('token')) {

            $usuario = new \Model\Usuario;
            $uid = $usuario->select("id_usuario",['email'=>$req->post('email')]);
            if ($uid && $uid['ativo'] == 1) {

                $code['id_usuario'] = end($uid);
                $code['link'] = bin2hex(random_bytes(32));
                $code['codigo'] = random_int(100000,999999);
                $code['data_expiracao'] = date("Y-m-d h-i-s",time()+600);
                $code['tipo'] = "ForgotPwd";
                $code['email'] = $req->post('email');

                $codigos = new \Model\Codigos;
                $mailer = new \Core\Mailer;

                $codigos->delete(['email'=>$req->post('email')]);

                if ($mailer->sendCode($code['email'],"Recuperar Senha",$code['codigo'])) {

                    $code['codigo'] = password_hash($code['codigo'],PASSWORD_DEFAULT);

                    $codigos->deleteExpired();
                    $codigos->insert($code) ? redirect("login/codigo/".$code['link']) : redirect('cadastro');
                    die();
                } else redirect("login");

            } else $errors['email'] = "Usuário não encontrado";

        }

        $ses->set('token',bin2hex(random_bytes(16)));

        $data['stage'] = "recuperar";
        $data['errors'] = $errors;
        $data['ses'] = $ses;
        $this->view("login_recuperar",$data);
    }

    // Function to take the code to restore the password
    public function codigo($link = false)
    {
        if (!$link) {
            redirect("login/recuperar");
            die();
        }
        $data = [];
        $errors = [];
        $codigos = new \Model\Codigos;
        $req = new \Core\Request;
        $ses = new \Core\Session;
        $ses->pop('alterar');
        $interval = 60-(time() - $ses->get('last_resend',time()-60));

        $row = $codigos->select("*",['link'=>$link,'tipo'=>'ForgotPwd']);
        if (!$row) {
            redirect("login/recuperar");
            die();
        }

        if ($req->get('resend') == 1){
            if (!$this->resend($row,"")) $errors['resend'] = "Limite de 60 minutos";
        } 

        if ($req->post() && $req->post('token') === $ses->pop('token')) {
            if ($req->post('code') === '' || strlen($req->post('code')) !== 6) {
                $errors['code'] = "O codigo precisa conter 6 números";
            } elseif (!password_verify($req->post('code'),$row['codigo'])) {
                $errors['code'] = "Codigo Incorreto";
            }

            if (empty($errors)) {
                $ses->set('alterar',bin2hex(random_bytes(32)));
                redirect("login/alterar/".$link."?ses=".$ses->get('alterar'));
                die();
            }
        }

        $ses->set('token',bin2hex(random_bytes(16)));

        $data['stage'] = "codigo";
        $data['interval'] = $interval;
        $data['errors'] = $errors;
        $data['ses'] = $ses;
        $data['row'] = $row;
        $this->view("login_recuperar",$data);
    }

    // Function to change the password
    public function alterar($link = false)
    {
        if (!$link) {
            redirect("login/recuperar");
            die();
        }
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $req = new \Core\Request;
        $codigos = new \Model\Codigos;
        if ($req->get('ses') !== $ses->get('alterar')) {
            redirect("login/codigo/".$link);
            die();
        }
        $row = $codigos->select("*",['link'=>$link,'tipo'=>'ForgotPwd']);
        if (!$row) {
            redirect("login/recuperar");
            die();
        }

        if ($req->posted() && $req->post('token') === $ses->pop('token')) {
            if ($req->post('senha') != $req->post('confirmacao_senha')) {
                $errors['senhas'] = "As senhas não são iguais";
            }
            $usuario = new \Model\Usuario;
            $usuario->validate($req->post(),'update');
            $errors = array_merge($errors,$usuario->getErrors());

            if (empty($errors)) {
                $hashed = password_hash($req->post('senha'),PASSWORD_BCRYPT,['cost'=>12]);
                if ($usuario->update(['senha'=>$hashed],['id_usuario'=>$row['id_usuario']])) {
                    $codigos->delete(['id_codigo'=>$row['id_codigo']]);
                    $ses->regenerate();
                    $ses->pop('alterar');
                    addNotification("Senha alterada","Atenção, sua senha foi alterada!",$row['id_usuario']);
                    message("Senha alterada com sucesso!");
                    redirect("login");
                }
            }
        }

        $ses->set('token',bin2hex(random_bytes(16)));

        $data['stage'] = "alterar";
        $data['errors'] = $errors;
        $data['ses'] = $ses;
        $data['row'] = $row;
        $this->view("login_recuperar",$data);
    }

    // Function to activate a account
    public function ativar($link = false)
    {
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $req = new \Core\Request;
        $codigos = new \Model\Codigos;
        $usuario = new \Model\Usuario;
        $interval = 60-(time() - $ses->get('last_resend',time()-60));
        $row = $codigos->select("*",['link'=>$link,'tipo'=>'2FA']);

        if (!$link || $req->get('active') != $ses->get('active') || !$row){
            redirect("login");
            die();
        } 

        if ($req->posted() && $req->post('token') === $ses->pop('token')) {
            if ($req->post('code') === '' || strlen($req->post('code')) !== 6) {
                $errors['code'] = "O codigo precisa conter 6 números";
            } elseif (!password_verify($req->post('code'),$row['codigo'])) {
                $errors['code'] = "Codigo Incorreto";
            }

            if (empty($errors)) {
                if ($usuario->update(['ativo'=>true],['id_usuario'=>$row['id_usuario']])){
                    $codigos->delete(['id_codigo'=>$row['id_codigo']]);
                    $user = $usuario->select("*",['id_usuario'=>$row['id_usuario']]);

                    if ($user) {
                        addNotification("Conta criada e verificada","Sua conta foi criada e verificada pelo e-mail com sucesso",$row['id_usuario']);
                        $ses->regenerate();
                        $ses->auth($user);
                        if ($req->get('remember') != '' && $req->get('remember')) {
                            $tokens = new \Model\Tokens;
                            $tokens->deleteExpired();
                            $tokens->remember_me($user['id_usuario']);
                        }
                    } else redirect("login");
                }
                redirect("login");
            }
        }

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['interval'] = $interval;
        $data['errors'] = $errors;
        $data['row'] = $row;
        $data['ses'] = $ses;
        $this->view('login_ativar',$data);
    }

    // Function to resend a code
    public function resend(){
        $ses = new \Core\Session;
        $req = new \Core\Request;
        $mailer = new \Core\Mailer;
        $codigos = new \Model\Codigos;

        if (!$req->posted()) {
            echo json_encode(['success'=>false]);
            redirect("login");
            die();
        }

        $row = $codigos->select("*",['link'=>$req->post("link"),'tipo'=>$req->post("type")]);
        if ($req->post("link") === '' || !$row){
            echo json_encode(['success'=>false]);
            redirect("login");
            die();
        } 

        if ($ses->get('last_resend') === ''){
            $ses->set('last_resend',time());
        } elseif (time() - $ses->get('last_resend') < 60) return false;

        $ses->set('last_resend',time());
        $codigos->delete(['id_usuario'=>$row['id_usuario']]);
        $codigos->deleteExpired();

        $login = $req->post('login');
        $get = $req->post('get');
        $subject = $req->post('subject');
        $codigo = random_int(100000,999999);
        $data['codigo'] = password_hash($codigo,PASSWORD_DEFAULT);
        $data['link'] = bin2hex(random_bytes(32));
        $data['tipo'] = $req->post("type");
        $data['data_expiracao'] = date("Y-m-d H-i-s",time()+600);
        $data['email'] = $row['email'];
        $data['id_usuario'] = $row['id_usuario'];

        if ($codigos->insert($data)){

            if ($req->post('get') !== '') $ses->set($get,bin2hex(random_bytes(16)));
            
            $mailer->sendCode($data['email'],$subject,$codigo);
            $redirect = "login/".$login."/".$data['link'];

            if ($req->post('get') !== '') $redirect .= "&".$get."=".$ses->get($get);
            echo json_encode(['success'=>true,'redirect'=>$redirect]);

            die();

        } else echo json_encode(['success'=>false]);
    }
}
