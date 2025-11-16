<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Admin class
 */
class Admin
{
    use Controller;

    public function index(){
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $req = new \Core\Request;

        $this->resetdb();

        if (!$ses->is_admin()) {
            redirect("moradias");
            die();
        }

        // If already confirmed go to dashboard
        if ($ses->get('confirmation')) {
            redirect('admin/dashboard');
            die();
        }

        if ($req->posted() && $req->post('token') === $ses->pop('token')) {
            if ($req->post('pwd') === '' || $req->post('pwd_confirmation') === '') {
                $errors['inputs'] = "Por favor preencha todos os campos";
            } else if ($req->post('pwd') !== $req->post('pwd_confirmation')) {
                $errors['inputs'] = "As senhas não são iguais";
            } 

            if (empty($errors)) {
                $usuario = new \Model\Usuario;
                $user = $usuario->select('senha',['id_usuario'=>$ses->getUser('id_usuario')]);
                if (password_verify($req->post('pwd'),$user['senha'])) {
                    $ses->set('confirmation',true);
                    redirect('admin/dashboard');
                    die();
                }
                $errors['inputs'] = "Senha incorreta";
            }
        }  

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['errors'] = $errors;
        $data['ses'] = $ses;
        $this->view('admin_confirmation',$data);
    }

    // Admin page
    public function dashboard($rejected = '')
    {
        $data = [];

        $data['rows'] = false;
        $ses = new \Core\Session;

        if (!$ses->is_admin() || !$ses->get('confirmation')) {
            redirect("admin");
            die();
        }
        
        $this->resetdb();

        $moradia = new \Model\Moradia;
        $usuario = new \Model\Usuario;

        $total_usuarios = $usuario->count("id_usuario");
        $total_moradias = $moradia->count("id_moradia");
        $total_aprovadas = $moradia->count("id_moradia",['situacao'=>'Aprovado']);
        $total_rejeitadas = $moradia->count("id_moradia",['situacao'=>'Rejeitado']);
        $total_analise = $moradia->count("id_moradia",['situacao'=>'Em Análise']);
        $total_hoje = $moradia->count("id_moradia",["data_criacao"=>date("Y-m-d")]);

        $old_date = date("Y-m-d",strtotime("-1 month"));
        $total_30dias = $moradia->manualCount("id_moradia",["(data_criacao BETWEEN ? AND ?)"],[$old_date,date("Y-m-d")]);

        $usuario->setLimit(50);
        $recent = $usuario->selectAll("id_usuario, nome_usuario, criacao");

        $max = 10;

        $pager = new \Core\Pager($total_analise,$max);
        $moradia->setOffset($pager->getOffset());
        $moradia->setLimit($max);

        $situacao = $rejected === '' ? "Em Análise" : "Rejeitado" ;

        $rows = $moradia->selectAll("*",['situacao'=>$situacao]);
        if ($rows){
            foreach ($rows as &$row) {
                $row_user = $usuario->select("email,email_contato,telefone",['id_usuario'=>$row['id_usuario']]);
                $row = array_merge($row,$row_user);
                unset($row['imagem2']);
                unset($row['imagem3']);
                unset($row['imagem4']);
                unset($row['imagem5']);
                unset($row['email_administrador']);
            }
            $data['rows'] = $rows;
        }

        $data['general'] = [
            'total_usuarios' => $total_usuarios,
            'total_moradias' => $total_moradias,
            'total_aprovadas' => $total_aprovadas,
            'total_rejeitadas' => $total_rejeitadas,
            'total_analise' => $total_analise,
            'total_30dias' => $total_30dias,
            'total_hoje' => $total_hoje,
            'recents' => $recent
        ];
        $data['situacao'] = $situacao;
        $data['pager'] = $pager;
        $data['ses'] = $ses;

        $this->view('admin',$data);
    }

    // function to approve a moradia
    public function approve()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;
        if (!$req->posted() || !($ses->is_admin() && $ses->get('confirmation',false))) {
            redirect("moradias");
            echo false;
            die();
        }

        $moradia = new \Model\Moradia;

        $uid = $moradia->select('id_usuario',['id_moradia'=>$req->post('id_moradia')]);
        if (!$uid) echo false;
        $uid = end($uid);

        if ($req->post('action') == "accept") {
            echo $moradia->update(['situacao'=>'Aprovado','data_rejeicao'=>null],['id_moradia'=>$req->post('id_moradia')]) ? true : false ;
            addNotification("Moradia Aprovada","Sua moradia foi aprovada! Logo ela aparecerá na lista principal.",$uid,"/moradia/id/".$req->post('id_moradia'));
        } else if ($req->post('action') == "reject") {
            echo $moradia->update(['situacao'=>'Rejeitado','data_rejeicao'=>date("Y-m-d")],['id_moradia'=>$req->post('id_moradia')]) ? true : false ;
            addNotification("Moradia Rejeitada","Sua moradia foi rejeitada! Por favor edite e confirme suas informações para uma nova verificação. (Moradias rejeitadas são deletadas após 7 dias)",$uid,"/moradia/id/".$req->post('id_moradia'));
        } else if ($req->post('action') == "suspend") {
            echo $moradia->update(['situacao'=>'Em Análise','data_rejeicao'=>null],['id_moradia'=>$req->post('id_moradia')]) ? true : false ;
            addNotification("Moradia Suspensa","Sua moradia foi suspensa! Foi percebido alguma suspeita a respeito de uma de suas moradia, ela será analisada novamente.",$uid,"/moradia/id/".$req->post('id_moradia'));
        } else if ($req->post('action') == "remove") {
            echo $moradia->delete(['id_moradia'=>$req->post('id_moradia')]) ? true : false ;
            addNotification("Moradia Removida","Sua moradia foi removida! Isso devido a alguma suspeita dos administradores.",$uid,"/moradia/id/".$req->post('id_moradia'));
        }

        die();
    }

    // function to delete a user
    public function deleteUser()
    {
        $ses = new \Core\Session;
        $req = new \Core\Request;

        if (!$req->posted() || !($ses->is_admin() && $ses->get('confirmation',false))) {
            redirect("perfil");
            echo false;
            die();
        }

        $usuario = new \Model\Usuario;
        $cargo = $usuario->select('cargo',['id_usuario'=>$req->post('id_usuario')]);
        if ($cargo['cargo'] == 'administrador') {
            echo false;
            die();
        }

        if ($usuario->delete(['id_usuario'=>$req->post('id_usuario')])) {
            echo true;
            die();
        }

        echo false;
    }

    private function resetdb(){
        $moradia = new \Model\Moradia;
        $usuario = new \Model\Usuario;
        $notif = new \Model\Notificacoes;
        $tokens = new \Model\Tokens;
        $codes = new \Model\Codigos;

        $moradia->deleteExpired();
        $usuario->deleteExpired();
        $notif->deleteExpired();
        $tokens->deleteExpired();
        $codes->deleteExpired();
    }
}
