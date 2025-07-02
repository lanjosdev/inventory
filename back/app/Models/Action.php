<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = "actions";
    protected $fillable = ['name', 'description'];
    protected $date = ['deleted_at'];

    public static function rules($id = null)
    {
        return [
            'name' => 'required|max:255|unique:actions,name' . ($id ? ",$id" : ''),
            'description' => 'nullable|max:255',
        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'name.unique' => 'Esta ação já está cadastrada.',
            'description.max' => 'A descrição deve ter no máximo 255 caracteres.'
        ];
    }
}
