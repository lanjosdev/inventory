<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exhibitor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'exhibitor_contacts', 'fk_exhibitor', 'fk_contact');
    }

    public static function rulesCreate()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',

            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255|min:3',
            'contacts.*.email' => 'required|email|max:255|min:7',
            'contacts.*.phone' => 'required|max:11',
            'contacts.*.observation' => 'nullable|max:255',
        ];
    }

    public static function feedbackCreate()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',

            'description.max' => 'O campo descrição deve ter no máximo 255 caracteres.',

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

    public static function rulesUpdate()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
        ];
    }

    public static function feedbackUpdate()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',

            'description.max' => 'O campo descrição deve ter no máximo 255 caracteres.',
        ];
    }
}
