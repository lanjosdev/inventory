<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        return [
            'name' => 'required|max:255|unique:permissions,name',
            'description' => 'nullable|max:255',
        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
            'name.unique' => 'O nome desta permissão já existe.',
            
            'description.max' => 'O campo descrição não pode ter mais que 255 caracteres.',
        ];
    }
}