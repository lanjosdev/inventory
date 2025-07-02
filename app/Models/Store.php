<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'fk_companie',
        'cnpj',
    ];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        return [
            'name' => 'required|max:255|',
            'fk_companie' => 'required|exists:companies,id',
            'cnpj' => 'required|digits:14|',

            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255|min:3',
            'contacts.*.email' => 'required|email|max:255|min:7',
            'contacts.*.phone' => 'required|max:11',
            'contacts.*.observation' => 'nullable|max:255',

        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',

            'fk_companie.required' => 'O campo rede é obrigatório.',
            'fk_companie.exists' => 'A rede selecionada não existe.',

            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.digits' => 'O CNPJ deve conter 14 caracteres.',

            'contacts.required' => 'É obrigatório informar ao menos um contato.',
            'contacts.array' => 'O campo contatos deve ser um array.',
            'contacts.min' => 'É obrigatório informar ao menos um contato.',
            'contacts.*.name.required' => 'O nome do contato é obrigatório.',
            'contacts.*.name.max' => 'O nome do contato deve ter no máximo 255 caracteres.',
            'contacts.*.name.min' => 'O nome do contato deve ter no mínimo 3 caracteres.',
            'contacts.*.email.required' => 'O e-mail do contato é obrigatório.',
            'contacts.*.email.email' => 'O e-mail do contato deve ser válido.',
            'contacts.*.email.max' => 'O e-mail do contato deve ter no máximo 255 caracteres.',
            'contacts.*.email.min' => 'O e-mail do contato deve ter no mínimo 7 caracteres.',
            'contacts.*.phone.required' => 'O telefone do contato é obrigatório.',
            'contacts.*.phone.max' => 'O telefone do contato deve ter no máximo 11 caracteres.',
            'contacts.*.observation.max' => 'A observação do contato deve ter no máximo 255 caracteres.',
        ];
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Companies::class, 'fk_companie');
    }

    public function addresses()
    {
        return $this->belongsToMany(\App\Models\Address::class, 'address_store', 'store_id', 'address_id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_stores', 'fk_store', 'fk_contact');
    }
}