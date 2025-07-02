<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemLog extends Model
{
    use SoftDeletes;

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