<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Usuario Class
 */
class Usuario
{
    use Model;

    protected $table = "usuario";
    protected $order = ['id_usuario'=>'DESC'];
    protected $allowedColumns = [
        'nome_usuario',
        'nome_completo',
        'email',
        'senha',
        'telefone',
        'email_contato',
        'ativo',
        'pfp'
    ];
    protected $onUpdateValidationRules = [
        'nome_usuario' => [
            'max_length=255',
            'no_spaces',
            'no_symbols_',
            'unique',
            'required'
        ],
        'nome_completo' => [
            'max_length=255',
            'no_numbers',
            'no_symbols',
            'unique',
        ],
        'email' => [
            'max_length=255',
            'no_spaces',
            'email',
            'unique',
            'required'
        ],
        'email_contato' => [
            'max_length=255',
            'no_spaces',
            'email',
            'unique',
        ],
        'telefone' => [
            'max_length=15',
            'min_length=15',
            'no_alpha',
            'phone_number',
            'unique',
        ],
        'senha' => [
            'min_length=8',
            'max_length=255',
            'required'
        ],
        'confirmacao_senha' => [
            'min_length=8',
            'max_length=255',
            'required'
        ],
        'ativo' => [
            'bool'
        ],
        'pfp' => [ 
            'image',
            'max_size=32'
        ]
    ];
    protected $onInsertValidationRules = [
        'nome_usuario' => [
            'max_length=255',
            'no_spaces',
            'no_symbols_',
            'unique',
            'required'
        ],
        'email' => [
            'max_length=255',
            'no_spaces',
            'email',
            'unique',
            'required'
        ],
        'senha' => [
            'min_length=8',
            'max_length=255',
            'required'
        ],
        'confirmacao_senha' => [
            'min_length=8',
            'max_length=255',
            'required'
        ],
    ];

    // Signup function
    public function signup(array $data)
    {
        if ($data['senha'] != $data['confirmacao_senha']) {
            $this->errors['senhas'] = "As senhas não são iguais";
        }
        if ($this->validate($data,"insert")) {
            // add extra data here

            $data['senha'] = password_hash($data['senha'],PASSWORD_BCRYPT,['cost'=>12]);

            if ($this->insert($data)) {
                message("Cadastro realizado com sucesso!");
                redirect("login");
                die();
            } else {
                message("Algo deu errado tente novamente...");
                redirect("cadastro");
                die();
            }

        }
    }

    public function login(array $data)
    {
        $row = $this->select("*",['email'=>$data['login'],'nome_usuario'=>$data['login']],"||");

        if ($row) {
            // Verifying Password
            if (password_verify($data['senha'],$row['senha'])) {
                // Setup data here
                $row['telefone'] = formatPhone($row['telefone']);
                unset($row['senha']);

                if (!$row['ativo']) {
                    $code = [];
                    $ses = new \Core\Session;
                    $mailer = new \Core\Mailer;
                    $codigos = new \Model\Codigos;
                    $row_code = $codigos->select("*",['id_usuario'=>$row['id_usuario']]);
                    $code['link'] = $row_code['link'] ?? "";
                    if (!$row_code) {
                        $codigos->delete(['id_usuario'=>$row['id_usuario']]);
                        $codigo = random_int(100000,999999);
                        $code['codigo'] = password_hash($codigo,PASSWORD_DEFAULT);
                        $code['link'] = bin2hex(random_bytes(32));
                        $code['tipo'] = "2FA";
                        $code['data_expiracao'] = date("Y-m-d H-i-s",time()+600);
                        $code['email'] = $row['email'];
                        $code['id_usuario'] = $row['id_usuario'];
                        $codigos->deleteExpired();
                        $codigos->insert($code) 
                        ? $mailer->sendCode($code['email'],"Verificar email",$codigo)
                        : redirect('login');
                    }
                    $ses->set('active',bin2hex(random_bytes(16)));
                    $redirect = "login/ativar/".$code['link']."&active=".$ses->get("active");
                    if (isset($data['remember']) && $data['remember']) $redirect .= "&remember=1";
                    redirect($redirect);
                    die();
                }

                $ses = new \Core\Session;
                $ses->regenerate();
                $ses->auth($row);

                if (isset($data['remember']) && $data['remember'] && $row['cargo'] != "administrador") {
                    $tokens = new \Model\Tokens;
                    $tokens->deleteExpired();
                    $tokens->remember_me($row['id_usuario']);
                }
                
                redirect('perfil');
                die();
            } else {
                $this->errors['login'] = "Nome de Usuário, email ou senha incorretos";
            }
        } else {
            $this->errors['login'] = "Nome de usuário, email ou senha incorretos";
        }
    }

    public function deleteExpired() {
        $users = $this->selectAll("id_usuario,criacao,ativo");
        $now = date_create(date("Y-m-d"));
        foreach ($users as $user) {
            $criacao = date_create($user['criacao']);
            $interval = date_diff($now,$criacao);
            if ($interval->days > 1 && $user['ativo'] == 0) {
                $this->delete(['id_usuario'=>$user['id_usuario']]);
            }
        }
    }
    
    public function setOrder(array $value, bool $default = false)
    {
        $this->order = $value;
        if ($default) $this->order = ['id_usuario'=>'DESC'];
    }
}
