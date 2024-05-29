<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'try',
        'created_at',
        'updated_at'
    ];
    protected $table = 'phone_verifications';
    public $incrementing = false;

}
