<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];
    protected $date = ['deleted_at'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'fk_role', 'fk_permission');
    }

    public static function rules()
    {
        return [
            'name' => 'required|max:255|unique:roles,name',
            'description' => 'nullable|max:255',
        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
            'name.unique' => 'O nome desse papel já existe.',

            'description.max' => 'A descrição não pode ter mais que 255 caracteres.'
        ];
    }
}