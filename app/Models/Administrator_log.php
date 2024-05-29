<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator_log extends Model
{
    use HasFactory;


    protected $fillable = [
        'admin_name',
        'admin_id',
        'target_name',
        'target_id',
        'target_server_id',
        'details'
    ];
    protected $table = 'administrator_log';
    public $incrementing = false;
}
