<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country',
        'state',
        'city',
        'address',
        'cep',
    ];
    protected $date = ['deleted_at'];

    public static function rules()
    {
        return [
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'city' => 'required|max:255',
            'address' => 'required|max:255',
            'cep' => 'required|max:9',
        ];
    }

    public static function feedback()
    {
        return [
            'country.required' => 'O campo pais é obrigatório.',
            'country.max' => 'O campo pais deve ter no máximo :max caracteres.',

            'state.required' => 'O campo estado é obrigatório.',
            'state.max' => 'O campo estado deve ter no máximo :max caracteres.',

            'city.required' => 'O campo cidade é obrigatório.',
            'city.max' => 'O campo cidade deve ter no máximo :max caracteres.',

            'address.required' => 'O campo endereço é obrigatório.',
            'address.max' => 'O campo endereço deve ter no máximo :max caracteres.',

            'cep.required' => 'O campo cep é obrigatório.',
            'cep.max' => 'O campo cep deve ter no máximo :max caracteres.',
        ];
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'address_store', 'address_id', 'store_id');
    }
}