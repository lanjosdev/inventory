<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;
    
    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'observation',
    ];

    public $timestamps = true;

    public static function rules()
    {
        return [
            'name' => 'required|max:255|min:3',
            'email' => 'required|email|max:255|min:7',
            'phone' => 'required|max:11',
            'observation' => 'nullable|max:255',

           
        ];
    }

    public static function feedback()
    {
        return [
            'name.required' => 'O nome do contato é obrigatório.',
            'name.max' => 'O nome do contato deve ter no máximo 255 caracteres.',
            'name.min' => 'O nome do contato deve ter no mínimo 3 caracteres.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'email.min' => 'O e-mail deve ter no mínimo 7 caracteres.',

            'phone.required' => 'O e-mail é obrigatório.',
            'phone.digits' => 'O telefone deve ter 11 caracteres.',

            'observation.max' => 'A observação deve ter no máximo 255 caracteres.',
        ];
    }

    public function companies()
    {
        return $this->belongsToMany(Companies::class, 'contact_companies', 'fk_contact', 'fk_companie');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'contact_stores', 'fk_contact', 'fk_store');
    }
}