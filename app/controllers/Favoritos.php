<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Favoritos class
 */
class Favoritos
{
    use Controller;

    public function index(){
        $data = [];
        $data['rows'] = false;
        $ses = new \Core\Session;

        // Check if the user is logged
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        $favoritos = new \Model\Favoritos;
        
        $total = $favoritos->count("id_moradia",['id_usuario'=>$ses->getUser('id_usuario')]);
        $max = 10;

        $pager = new \Core\Pager($total,$max);
        
        $favoritos->setOffset($pager->getOffset());
        $favoritos->setLimit($max);

        $favorites = $favoritos->selectAll('id_moradia',['id_usuario'=>$ses->getUser('id_usuario')]);

        $moradia = new \Model\Moradia;
        $rows = [];
        foreach ($favorites as $key => $value) {
            $rows[] = $moradia->select("*",['id_moradia'=>$value['id_moradia']]);
        }

        if (!empty($rows)){
            $usuario = new \Model\Usuario;
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
            $data['favorites'] = $favorites;
        }

        $data['pager'] = $pager;
        $data['ses'] = $ses;

        $this->view('favoritos',$data);
    }

    // Remove favorite
    public function favorite()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;
        if (!$req->posted() || !$ses->is_logged()) {
            echo json_encode(['success'=>false]);
            die();
        }

        $favoritos = new \Model\Favoritos;

        $data = ['id_moradia'=>$req->post('id_moradia'),'id_usuario'=>$ses->getUser('id_usuario')];

        if ($favoritos->count("*",$data) == 1) {
            $favoritos->delete($data);
            echo json_encode(['success'=>true]);
            die();
        }
    }
}
