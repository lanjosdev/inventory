<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactStores extends Model
{
    use SoftDeletes;
    protected $table = 'contact_stores';
    protected $fillable = [
        'fk_store',
        'name',
        'email',
        'phone',
        'observation'
    ];

    public static function rules()
    {
        return [
            'fk_store' => 'required|integer|exists:stores,id',
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|max:20',
            'observation' => 'nullable|max:255',
        ];
    }
    public static function feedback()
    {
        return [
            'fk_store.required' => 'A loja é obrigatória.',
            'fk_store.integer' => 'A loja deve ser um ID válido.',
            'fk_store.exists' => 'A loja informada não existe.',
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
