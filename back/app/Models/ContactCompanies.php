<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactCompanies extends Model
{
    use SoftDeletes;
    protected $table = 'contact_companies';
    protected $fillable = [
        'fk_rede',
        'name',
        'email',
        'phone',
        'observation'
    ];

    public static function rules()
    {
        return [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'max:20',
            'observation' => 'nullable|max:255',
        ];
    }
    public static function feedback()
    {
        return [
            'name.required' => 'O nome do contato é obrigatório.',
            'name.max' => 'O nome do contato deve ter no máximo 255 caracteres.',
            'name.min' => 'O nome do contato deve ter no mínimo 2 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'phone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'observation.max' => 'A observação deve ter no máximo 255 caracteres.'
        ];
    }
}