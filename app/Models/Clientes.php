<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'ie_rg', 'cpf_cnpj', 'contribuinte', 'cep', 'rua', 'numero', 'codigo_municipio', 
        'codigo_pais', 'pais', 'bairro', 'logradouro', 'municipio', 'uf', 'logradouro', 'telefone', 'email', 'complemento'
    ];

    public static function rules()
    {
        return [
            'nome' => 'required|string',
            'cpf_cnpj' => 'required|string',           
            'contribuinte' => 'required|boolean',
            'cep' => 'required|string',
            'rua' => 'required|string',
            'numero' => 'required|string',
            'codigo_municipio' => 'required|numeric',
            'codigo_pais' => 'required|numeric',
            'pais' => 'required|string',
            'bairro' => 'required|string',
            'logradouro' => 'string|nullable',
            'municipio' => 'required|string',
            'uf' => 'required|string',
            'complemento' => 'string|nullable',
            'telefone' => 'required|string',
            'email' => 'string',
        ];
    }
}
