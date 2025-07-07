<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'fk_store',
        'fk_sector',
        'fk_asset_type',
        'fk_status',
        'observation',
        'quantity'
    ];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        return [
            'name' => 'required|max:255',
            'fk_store' => 'required|integer|exists:stores,id',
            'fk_sector' => 'required|integer|exists:sectors,id',
            'fk_asset_type' => 'required|integer|exists:asset_types,id',
            'fk_status' => 'required|integer|exists:statuses,id',
            'observation' => 'nullable|max:255',
            'quantity' => 'required|min:1|max:1000|integer',
        ];
    }
    public static function feedbacks()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome não pode exceder 255 caracteres.',

            'fk_store.required' => 'O campo loja é obrigatório.',
            'fk_store.integer' => 'O campo loja deve ser um número inteiro.',
            'fk_store.exists' => 'A loja selecionada é inválida.',

            'fk_sector.required' => 'O campo setor é obrigatório.',
            'fk_sector.integer' => 'O campo setor deve ser um número inteiro.',
            'fk_sector.exists' => 'O setor selecionado é inválido.',

            'fk_asset_type.required' => 'O campo tipo de ativo é obrigatório.',
            'fk_asset_type.integer' => 'O campo tipo de ativo deve ser um número inteiro.',
            'fk_asset_type.exists' => 'O tipo de ativo selecionado é inválido.',

            'fk_status.required' => 'O campo status é obrigatório.',
            'fk_status.integer' => 'O campo status deve ser um número inteiro.',
            'fk_status.exists' => 'O status selecionado é inválido.',

            'observation.max' => 'O campo observação deve ter até 255 caracteres.',

            'quantity.required' => 'O campo quantidade é obrigatório.',
            'quantity.min' => 'O campo quantidade deve ser no mínimo 1.',
            'quantity.max' => 'O campo quantidade deve ser no máximo 1000.',
            'quantity.integer' => 'O campo quantidade deve ser um número inteiro.',
        ];
    }
}