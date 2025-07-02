<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        return [
            'name' => 'required|max:255|min:2',
            'description' => 'nullable|max:255',
        ];
    }
    public static function feedback()
    {
        return [
            'name.required' => 'O nome do setor é obrigatório.',
            'name.max' => 'O nome do setor deve ter no máximo 255 caracteres.',
            'name.min' => 'O nome do setor deve ter no mínimo 2 caracteres.',
            'description.max' => 'A descrição deve ter no máximo 255 caracteres.'
        ];
    }
}
