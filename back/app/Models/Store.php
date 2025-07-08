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

    public static function rulesCreate()
    {
        return [
            'name' => 'required|max:255|min:5',
            'fk_companie' => 'required|exists:companies,id',
            'cnpj' => 'required|digits:14|',

            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255|min:3',
            'contacts.*.email' => 'required|email|max:255|min:7',
            'contacts.*.phone' => 'required|max:11',
            'contacts.*.observation' => 'nullable|max:255',

            'country',
            'state',
            'city',
            'address',
            'cep',

            'address' => 'required|min:1',
            'address.*.country' => 'required|max:255',
            'address.*.state' => 'required|email|max:255',
            'address.*.city' => 'required|max:255',
            'address.*.address' => 'required|max:255',
            'address.*.cep' => 'required|max:255',
        ];
    }

    public static function feedbackCreate()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'name.min' => 'O nome deve ter no mínimo 5 caracteres.',

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

            'address.required' => 'É obrigatório informar ao menos um endereço.',
            'address.min' => 'É obrigatório informar ao menos um endereço.',
            'address.*.country.required' => 'O país do endereço é obrigatório.',
            'address.*.country.max' => 'O país do endereço deve ter no máximo 255 caracteres.',
            'address.*.state.required' => 'O estado do endereço é obrigatório.',
            'address.*.state.email' => 'O estado do endereço deve ser um e-mail válido.',
            'address.*.state.max' => 'O estado do endereço deve ter no máximo 255 caracteres.',
            'address.*.city.required' => 'A cidade do endereço é obrigatória.',
            'address.*.city.max' => 'A cidade do endereço deve ter no máximo 255 caracteres.',
            'address.*.address.required' => 'O endereço é obrigatório.',
            'address.*.address.max' => 'O endereço deve ter no máximo 255 caracteres.',
            'address.*.cep.required' => 'O CEP do endereço é obrigatório.',
            'address.*.cep.max' => 'O CEP do endereço deve ter no máximo 255 caracteres.',
        ];
    }

    public static function rulesUpdate()
    {
        return [
            'name' => 'required|max:255|min:5',
            'fk_companie' => 'required|exists:companies,id',
            'cnpj' => 'required|digits:14|',
        ];
    }

    public static function feedbackUpdate()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'name.min' => 'O nome deve ter no mínimo 5 caracteres.',

            'fk_companie.required' => 'O campo rede é obrigatório.',
            'fk_companie.exists' => 'A rede selecionada não existe.',

            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.digits' => 'O CNPJ deve conter 14 caracteres.',
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