<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Codigos Class
 */
class Codigos
{
    use Model;

    protected $table = "codigos";
    protected $order = ['id_codigo'=>'DESC'];
    protected $allowedColumns = [
        "codigo",
        "tipo",
        "link",
        "data_expiracao",
        "email",
        "id_usuario"
    ];
    protected $onUpdateValidationRules = [
        "codigo" => [
            'required'
        ],
        "tipo" => [
            'in_array=2FA,ForgotPwd',
            'required'
        ],
        "link" => [
            'required'
        ],
        "data_expiracao" => [
            'datetime',
            'required'
        ],
        "email" => [
            'email'
        ],
        "id_usuario" => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
        ]
    ];
    protected $onInsertValidationRules = [
        "codigo" => [
            'required'
        ],
        "tipo" => [
            'in_array=2FA,ForgotPwd',
            'required'
        ],
        "link" => [
            'required'
        ],
        "data_expiracao" => [
            'datetime',
            'required'
        ],
        "email" => [
            'email'
        ],
        "id_usuario" => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
        ]
    ];
    public function setOrder(array $value, bool $default = false)
    {
        $this->order = $value;
        if ($default) $this->order = ['data_favorito'=>'DESC'];
    }

    public function deleteExpired() {
        $codes = $this->selectAll("id_codigo,data_expiracao");
        $now = date_create(date("Y-m-d h:i:s"));
        foreach ($codes as $code) {
            $expiry = date_create($code['data_expiracao']);
            if ($now > $expiry) {
                $this->delete(['id_codigo'=>$code['id_codigo']]);
            }
        }
    }
}
