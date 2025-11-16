<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Favoritos Class
 */
class Favoritos
{
    use Model;

    protected $table = "favoritos";
    protected $order = ['data_favorito'=>'DESC'];
    protected $allowedColumns = [
        'id_moradia',
        'id_usuario'
    ];
    protected $onUpdateValidationRules = [
        "id_moradia" => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
            'required'
        ],
        "id_usuario" => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
            'required'
        ]
    ];
    protected $onInsertValidationRules = [
        "id_moradia" => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
            'required'
        ],
        "id_usuario" => [
            'max_length=11',
            'min_length=1',
            'no_alpha',
            'no_symbols',
            'no_spaces',
            'required'
        ]
    ];
    public function setOrder(array $value, bool $default = false)
    {
        $this->order = $value;
        if ($default) $this->order = ['data_favorito'=>'DESC'];
    }
}
