<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Companies extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_contact',
        'email',
        'phone',
        'observation'
    ];

    protected $date = ['deleted_at'];

    // app/Models/Company.php
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            // Se a company for deletada de verdade (forceDelete), deleta as lojas também
            if ($company->isForceDeleting()) {
                $company->stores()->forceDelete();
            } else {
                // Se for um soft delete, faz o soft delete das lojas junto
                $company->stores()->delete();
            }
        });
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_companies', 'fk_companie', 'fk_contact');
    }

    public static function rulesCreate()
    {
        return [
            'name' => 'required|max:255|min:2',

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
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.max' => 'O nome da empresa deve ter no máximo 255 caracteres.',
            'name.min' => 'O nome da empresa deve ter no mínimo 2 caracteres.',

            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255|min:3',
            'contacts.*.email' => 'required|email|max:255|min:7',
            'contacts.*.phone' => 'required|max:11',
            'contacts.*.observation' => 'nullable|max:255',
        ];
    }
    
    public static function rulesUpdate()
    {
        return [
            'name' => 'required|max:255|min:2',
        ];
    }
    public static function feedbackUpdate()
    {
        return [
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.max' => 'O nome da empresa deve ter no máximo 255 caracteres.',
            'name.min' => 'O nome da empresa deve ter no mínimo 2 caracteres.',
        ];
    }
}