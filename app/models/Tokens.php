<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Tokens Class
 */
class Tokens
{
    use Model;

    protected $table = "tokens";
    protected $order = ['id_token'=>'DESC'];
    protected $allowedColumns = [
        'seletor',
        'validador',
        'data_expiracao',
        'id_usuario'
    ];
    protected $onUpdateValidationRules = [
        'seletor' => [
            'required'
        ],
        'validador' => [
            'required'
        ],
        'data_expiracao' => [
            'date',
            'required'
        ],
        'id_usuario' => [
            'max_length=11',
            'min_length=1',
            'no_spaces',
            'no_symbols',
            'no_alpha',
            'required'
        ]
    ];
    protected $onInsertValidationRules = [
        'seletor' => [
            'required'
        ],
        'validador' => [
            'required'
        ],
        'data_expiracao' => [
            'date',
            'required'
        ],
        'id_usuario' => [
            'max_length=11',
            'min_length=1',
            'no_spaces',
            'no_symbols',
            'no_alpha',
            'required'
        ]
    ];

    public function generate_tokens() : array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        return [$selector, $validator, $selector . ":" . $validator];
    }

    public function split_token(string $token) : array|bool
    {
        $split = explode(":",$token);

        if ($split && count($split) == 2) {
            return $split;
        }

        return false;
    }

    public function add($uid, string $selector, string $hashedValidator, string $expiry) : bool
    {
        $data = [
            'id_usuario' => $uid,
            'seletor' => $selector,
            'validador' => $hashedValidator,
            'data_expiracao' => $expiry
        ];

        if ($this->insert($data)) {
            return true;
        }
        return false;
    }

    public function remove($uid) : bool
    {
        if ($this->delete(['id_usuario'=>$uid])) {
            return true;
        }
        return false;
    }

    public function find_user(string $token) : bool|array
    {
        $tokens = $this->split_token($token);

        if (!$tokens) return false;

        $token_row = $this->select("*",['seletor'=>$tokens[0]]);
        if (!$token_row) return false;

        $date_now = date_create(date("Y-m-d"));
        $token_row['data_expiracao'] = date_create($token_row['data_expiracao']);
        if (date_diff($token_row['data_expiracao'],$date_now)->days > 30) {
            $this->remove($token_row['id_usuario']);
            return false;
        }

        if (!password_verify($tokens[1],$token_row['validador'])) {
            return false;
        }

        $usuario = new \Model\Usuario;
        $user = $usuario->select("*",['id_usuario'=>$token_row['id_usuario']]);

        if ($user) return $user;

        return false;
    }

    public function remember_me($uid, $days = 30) : bool
    {
        [$selector, $validator, $token] = $this->generate_tokens();

        $this->remove($uid);

        $seconds = time() + (60 * 60 * 24 * $days);
        $expiry = date("Y-m-d",$seconds);

        $hashedValidator = password_hash($validator,PASSWORD_DEFAULT);

        if ($this->add($uid,$selector,$hashedValidator,$expiry)) {
            setcookie('remember_me',$token,$seconds,"/","",true,true);
        }

        return false;
    }

    public function setOrder(array $value, bool $default = false)
    {
        $this->order = $value;
        if ($default) $this->order = ['favorito_data'=>'DESC'];
    }

    public function deleteExpired() {
        $tokens = $this->selectAll("id_token,data_expiracao");
        $now = date_create(date("Y-m-d"));
        foreach ($tokens as $token) {
            $expiry = date_create($token['data_expiracao']);
            if ($now > $expiry) {
                $this->delete(['id_token'=>$token['id_token']]);
            }
        }
    }
}
