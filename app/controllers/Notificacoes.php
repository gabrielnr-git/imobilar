<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Notificacoes class
 */
class Notificacoes
{
    use Controller;

    public function index(){
        $data = [];
        $ses = new \Core\Session;

        if (!$ses->is_logged()) {
            redirect('moradias');
            die();
        }

        $notif = new \Model\Notificacoes;
        $notif->deleteExpired();
        $pager = new \Core\Pager($notif->total($ses->getUser('id_usuario')),15);

        $notif->setLimit(15);
        $notif->setOffset($pager->getOffset());

        $rows = $notif->getAll($ses->getUser('id_usuario'));

        $date_now = date_create(date("Y-m-d"));
        foreach ($rows as &$row) {
            $date_notif = date_create($row['data_notificacao']);
            $interval = date_diff($date_notif,$date_now);
            $row['days'] = $interval->days;
        }

        $ses->set('token',bin2hex(random_bytes(16)));

        $data['rows'] = $rows;
        $data['pager'] = $pager;
        $data['ses'] = $ses;
        $this->view('notificacoes',$data);
    }

    // Function to read a notification
    public function read()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;

        if (!$req->posted() || $req->post('token') !== $ses->get('token')) {
            redirect('notificacoes');
            echo false;
            die();
        }

        $notif = new \Model\Notificacoes;

        $user_id = $notif->select("id_usuario",['id_notificacao'=>$req->post('id_notificacao')]);
        $user_id = end($user_id);

        if ($user_id !== $ses->getUser('id_usuario')) {
            echo false;
            die();
        }
        
        if ($notif->update(['lido'=>1],['id_notificacao'=>$req->post('id_notificacao')])) {
            echo true;
            die();
        } 
        echo false;
        die();
    }

    // Function to read all notifications
    public function readAll()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;

        if (!$req->posted() || $req->post('token') !== $ses->get('token')) {
            redirect('notificacoes');
            echo false;
            die();
        }

        $notif = new \Model\Notificacoes;

        $unreaded = $notif->getUnread($ses->getUser('id_usuario'));
        
        if ($unreaded) {
            foreach ($unreaded as $unread) {
                if (!$notif->update(['lido'=>1],['id_notificacao'=>$unread['id_notificacao']])){
                    echo false;
                    die();
                }
            }
            echo true;
            die();
        }
        echo false;
        die();
    }

    // Function to remove a notification
    public function remove()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;

        if (!$req->posted() || $req->post('token') !== $ses->get('token')) {
            redirect('notificacoes');
            echo false;
            die();
        }

        $notif = new \Model\Notificacoes;

        $user_id = $notif->select("id_usuario",['id_notificacao'=>$req->post('id_notificacao')]);
        $user_id = end($user_id);

        if ($user_id !== $ses->getUser('id_usuario')) {
            echo false;
            die();
        }
        
        if ($notif->delete(['id_notificacao'=>$req->post('id_notificacao')])) {
            echo true;
            die();
        }
        echo false;
        die();
    }

    // Function to remove all notifications
    public function removeAll()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;

        if (!$req->posted() || $req->post('token') !== $ses->get('token')) {
            redirect('notificacoes');
            echo false;
            die();
        }

        $notif = new \Model\Notificacoes;

        if ($req->post('action') == 'all') {
            echo $notif->delete(['id_usuario'=>$ses->getUser('id_usuario')]) ? true : false ;
            die();
        } elseif ($req->post('action') == 'readed') {
            echo $notif->delete(['id_usuario'=>$ses->getUser('id_usuario'),'lido'=>1]) ? true : false ;
            die();
        }
        echo false;
        die();
    }
}
