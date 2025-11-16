<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Publicacoes class
 */
class Publicacoes
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

        $moradia = new \Model\Moradia;
        $moradia->deleteExpired();

        // Total of moradias
        $total = $moradia->count("id_moradia",['id_usuario'=>$ses->getUser('id_usuario')]);
        $max = 10;

        // Pager setup
        $pager = new \Core\Pager($total,$max);
        $moradia->setOffset($pager->getOffset());
        $moradia->setLimit($max);

        // Setup the rows to display
        $rows = $moradia->selectAll("*",['id_usuario'=>$ses->getUser('id_usuario')]);
        if ($rows){
            foreach ($rows as &$row) {
                unset($row['imagem2']);
                unset($row['imagem3']);
                unset($row['imagem4']);
                unset($row['imagem5']);
                unset($row['email_administrador']);
            }
            $data['rows'] = $rows;
        }

        $data['pager'] = $pager;
        $data['ses'] = $ses;
        $this->view('publicacoes',$data);
    }
}
