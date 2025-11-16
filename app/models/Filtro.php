<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Filtro Class
 */
class Filtro
{
    use Model;

    protected $allowedColumns = [
        'id_moradia',
        'id_usuario'
    ];
    protected $onUpdateValidationRules = [
        'cep' => [
            'cep',
            'max_length=9',
            'min_length=9',
            'no_spaces',
            'no_alpha'
        ],
        'cidade' => [
            'max_length=32',
            'min_length=4',
            'no_numbers',
            'no_symbols'
        ],
        'uf' => [
            'in_array=Acre,Alagoas,Amapá,Amazonas,Bahia,Ceará,Distrito Federal,Espírito Santo,Goiás,Maranhão,Mato Grosso,Mato Grosso do Sul,Minas Gerais,Pará,Paraíba,Paraná,Pernambuco,Piauí,Rio de Janeiro,Rio Grande do Norte,Rio Grande do Sul,Rondônia,Roraima,Santa Catarina,São Paulo,Sergipe,Tocantins',
            'max_length=19',
            'min_length=4',
            'no_symbols',
            'no_numbers'
        ],
        'preco_min' => [
            'max_length=13',
            'price'
        ],
        'preco_max' => [
            'max_length=13',
            'price'
        ],
        'numero_comodos' => [
            'no_spaces',
            'no_symbols',
            'no_alpha'
        ],
        'casa' => [
            'bool'
        ],
        'apartamento' => [
            'bool'
        ],
        'kitnet' => [
            'bool'
        ],
        'república' => [
            'bool'
        ],
        'quarto' => [
            'bool'
        ],
        'quarto_compartilhado' => [
            'bool'
        ],
        'dormitório' => [
            'bool'
        ],
        'pensão' => [
            'bool'
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
        ]
    ];
}
