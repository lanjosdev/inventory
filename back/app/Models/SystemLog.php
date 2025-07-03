<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "system_logs";
    protected $fillable = [
        'fk_user',
        'fk_action',
        'name_table',
        'record_id',
        'description',
    ];
    protected $date = ['deleted_at'];
}
