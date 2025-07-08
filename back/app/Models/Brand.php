<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'observation'
    ];

    protected $date = ['deleted_at'];

    public static function rules() {
        return [
            'name' => 'required|max:255',
            'observation' => 'nullable|max:255'
        ];
    }

    public static function feedback() {
        return [
            'name.required' => 'O campo nome é obrigatório',
            'name.max' => 'O campo nome deve ter no máximo 255 caracteres',
            
            'observation.max' => 'O campo observação deve ter no máximo 255 caracteres'
        ];
    }
}