<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Notificacoes Class
 */
class Notificacoes
{
    use Model;

    protected $table = "notificacoes";
    protected $order = ['lido'=>'ASC','id_notificacao'=>'DESC'];
    protected $allowedColumns = [
        'assunto',
        'conteudo',
        'lido',
        'link',
        'id_usuario'
    ];
    protected $onUpdateValidationRules = [
        'assunto' => [
            'max_length=255',
            'min_length=1',
            'required'
        ],
        'conteudo' => [
            'max_lenght=65525',
            'min_length=1',
            'required'
        ],
        'lido' => [
            'bool',
            'required'
        ],
        'link' => [
            'max_length=255',
            'no_spaces'
        ],
        'id_usuario' => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
            'required'
        ]
    ];
    protected $onInsertValidationRules = [
        'assunto' => [
            'max_length=255',
            'min_length=1',
            'required'
        ],
        'conteudo' => [
            'max_lenght=65535',
            'min_length=1',
            'required'
        ],
        'link' => [
            'max_length=255',
            'no_spaces'
        ],
        'id_usuario' => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
            'required'
        ]
    ];
    
    public function add(string $title, string $content,$uid) : bool
    {
        $notification = [
            'assunto' => $title,
            'conteudo' => $content,
            'id_usuario' => $uid
        ];
        if ($this->insert($notification)) {
            return true;
        }
        return false;
    }

    public function mark_read($id) : bool
    {
        if ($this->update(['lido'=>1],['id_notificacao'=>$id])){
            return true;
        }
        return false;
    }

    public function getAll($uid) : array|bool
    {
        return $this->selectAll("*",['id_usuario'=>$uid]);
    }

    public function getUnread($uid) : array|bool
    {
        return $this->selectAll("*",['id_usuario'=>$uid,'lido'=>0]);
    }

    public function total($uid) : int
    {
        return $this->count("id_notificacoes",['id_usuario'=>$uid]);
    }

    public function totalUnread($uid) : int
    {
        return $this->count("id_notificacoes",['id_usuario'=>$uid,'lido'=>0]);
    }

    public function deleteExpired() {
        $notifs = $this->selectAll("id_notificacao,data_notificacao,lido");
        $now = date_create(date("Y-m-d"));
        foreach ($notifs as $notif) {
            $creation = date_create($notif['data_notificacao']);
            $interval = date_diff($creation,$now);
            if ($interval->days > 7 && $notif['lido'] == 1){
                $this->delete(['id_notificacao'=>$notif['id_notificacao']]);
            }
            if ($interval->days > 31 && $notif['lido'] == 0){
                $this->delete(['id_notificacao'=>$notif['id_notificacao']]);
            }
        }
    }

    public function setOrder(array $value, bool $default = false)
    {
        $this->order = $value;
        if ($default) $this->order = ['lido'=>'DESC','id_notificacao'=>'DESC'];
    }
}
