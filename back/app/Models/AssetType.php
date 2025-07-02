<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'observation',
    ];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        
        return [
            'name' => 'required|max:255|unique:asset_types,name',
            'observation' => 'nullable|max:255',
        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'name.unique' => 'Este tipo de ativo já está cadastrado.',
            
            'observation.max' => 'A observação deve ter no máximo 255 caracteres.'
        ];
    }
}