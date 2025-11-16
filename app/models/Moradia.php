<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Moradia Class
 */
class Moradia
{
    use Model;

    protected $table = "moradia";
    protected $order = ['id_moradia'=>'DESC'];
    protected $allowedColumns = [
        'nome',
        'tipo',
        'largura',
        'comprimento',
        'preco',
        'numero_comodos',
        'cep',
        'cidade',
        'uf',
        'logradouro',
        'bairro',
        'numero',
        'descricao',
        'wifi',
        'refeicao',
        'lazer',
        'estacionamento',
        'animais',
        'imagem1',
        'imagem2',
        'imagem3',
        'imagem4',
        'imagem5',
        'situacao',
        'data_rejeicao',
        'id_usuario',
    ];
    protected $onUpdateValidationRules = [
        'nome' => [
            'max_length=255',
            'no_symbols',
            'required'
        ],
        'tipo' => [
            'max_length=21',
            'min_length=4',
            'in_array=Casa,Apartamento,Kitnet,República,Quarto,Quarto Compartilhado,Dormitório,Pensão',
            'required'
        ],
        'largura' => [
            'max_length=5',
            'no_symbols',
            'no_alpha',
            'no_spaces',
            'required'
        ],
        'comprimento' => [
            'max_length=5',
            'no_symbols',
            'no_alpha',
            'no_spaces',
            'required'
        ],
        'preco' => [
            'max_length=13',
            'price',
            'required'
        ],
        'numero_comodos' => [
            'no_spaces',
            'no_symbols',
            'no_alpha',
            'required'
        ],
        'cep' => [
            'cep',
            'max_length=9',
            'min_length=9',
            'no_spaces',
            'no_alpha',
            'required'
        ],
        'cidade' => [
            'max_length=32',
            'min_length=4',
            'no_numbers',
            'no_symbols',
            'required'
        ],
        'uf' => [
            'in_array=Acre,Alagoas,Amapá,Amazonas,Bahia,Ceará,Distrito Federal,Espírito Santo,Goiás,Maranhão,Mato Grosso,Mato Grosso do Sul,Minas Gerais,Pará,Paraíba,Paraná,Pernambuco,Piauí,Rio de Janeiro,Rio Grande do Norte,Rio Grande do Sul,Rondônia,Roraima,Santa Catarina,São Paulo,Sergipe,Tocantins',
            'max_length=19',
            'min_length=4',
            'no_symbols',
            'no_numbers',
            'required'
        ],
        'logradouro' => [
            'max_length=255',
            'no_symbols',
            'no_numbers',
            'required'
        ],
        'bairro' => [
            'max_length=255',
            'no_symbols',
            'no_numbers',
            'required'
        ],
        'numero' => [
            'max_length=3',
            'no_spaces',
            'no_symbols',
            'no_alpha',
            'required'
        ],
        'descricao' => [
            'max_length=65535',
            'required'
        ],
        'situacao' => [
            'in_array=Aprovado,Reprovado,Em analise',
            'max_length=10',
            'min_length=8',
            'no_numbers',
            'no_symbols',
            'no_spaces',
            'required'
        ],
        'wifi' => [
            'bool'
        ],
        'refeicao' => [
            'bool'
        ],
        'lazer' => [
            'bool'
        ],
        'estacionamento' => [
            'bool'
        ],
        'animais' => [
            'bool'
        ],
        'imagem1' => [
            'max_size=50',
            'image',
            'required'
        ],
        'imagem2' => [
            'max_size=50',
            'image'
        ],
        'imagem3' => [
            'max_size=50',
            'image'
        ],
        'imagem4' => [
            'max_size=50',
            'image'
        ],
        'imagem5' => [
            'max_size=50',
            'image'
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
        'nome' => [
            'max_length=255',
            'no_symbols',
            'required'
        ],
        'tipo' => [
            'max_length=21',
            'min_length=4',
            'in_array=Casa,Apartamento,Kitnet,República,Quarto,Quarto Compartilhado,Dormitório,Pensão',
            'required'
        ],
        'largura' => [
            'max_length=5',
            'no_symbols',
            'no_alpha',
            'no_spaces',
            'required'
        ],
        'comprimento' => [
            'max_length=5',
            'no_symbols',
            'no_alpha',
            'no_spaces',
            'required'
        ],
        'preco' => [
            'price',
            'required'
        ],
        'numero_comodos' => [
            'no_spaces',
            'no_symbols',
            'no_alpha',
            'required'
        ],
        'cep' => [
            'cep',
            'max_length=9',
            'min_length=9',
            'no_spaces',
            'no_alpha',
            'required'
        ],
        'cidade' => [
            'max_length=32',
            'min_length=4',
            'no_numbers',
            'no_symbols',
            'required'
        ],
        'uf' => [
            'in_array=Acre,Alagoas,Amapá,Amazonas,Bahia,Ceará,Distrito Federal,Espírito Santo,Goiás,Maranhão,Mato Grosso,Mato Grosso do Sul,Minas Gerais,Pará,Paraíba,Paraná,Pernambuco,Piauí,Rio de Janeiro,Rio Grande do Norte,Rio Grande do Sul,Rondônia,Roraima,Santa Catarina,São Paulo,Sergipe,Tocantins',
            'max_length=19',
            'min_length=4',
            'no_symbols',
            'no_numbers',
            'required'
        ],
        'logradouro' => [
            'max_length=255',
            'no_symbols',
            'no_numbers',
            'required'
        ],
        'bairro' => [
            'max_length=255',
            'no_symbols',
            'no_numbers',
            'required'
        ],
        'numero' => [
            'max_length=3',
            'no_spaces',
            'no_symbols',
            'no_alpha',
            'required'
        ],
        'descricao' => [
            'max_length=65535',
            'required'
        ],
        'situacao' => [
            'in_array=Aprovado,Reprovado,Em analise',
            'max_length=10',
            'min_length=8',
            'no_numbers',
            'no_symbols',
            'no_spaces',
            'required'
        ],
        'wifi' => [
            'bool'
        ],
        'refeicao' => [
            'bool'
        ],
        'lazer' => [
            'bool'
        ],
        'estacionamento' => [
            'bool'
        ],
        'animais' => [
            'bool'
        ],
        'imagem1' => [
            'max_size=100',
            'image',
            'required'
        ],
        'imagem2' => [
            'max_size=100',
            'image'
        ],
        'imagem3' => [
            'max_size=100',
            'image'
        ],
        'imagem4' => [
            'max_size=100',
            'image'
        ],
        'imagem5' => [
            'max_size=100',
            'image'
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
    
    public function deleteExpired() {
        $rows = $this->selectAll("id_moradia,situacao,data_rejeicao");
        $now = date_create(date("Y-m-d"));
        foreach ($rows as $row) {
            if ($row['situacao'] == "Rejeitado") {
                $rejeicao = date_create($row['data_rejeicao']);
                $interval = date_diff($now,$rejeicao);
                if ($interval->days > 7) {
                    $this->delete(['id_moradia'=>$row['id_moradia']]);
                }
            }
            
        }
    }

    public function setOrder(array $value, bool $default = false)
    {
        $this->order = $value;
        if ($default) $this->order = ['id_moradia'=>'DESC'];
    }
}
