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

    
    
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'address_store', 'address_id', 'store_id');
    }
}