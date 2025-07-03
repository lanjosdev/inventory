<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        return [
            'name' => 'required|max:255|unique:roles,name',
        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
            'name.exists' => 'O nome desse cargo inserido já existe.',
        ];
    }
}