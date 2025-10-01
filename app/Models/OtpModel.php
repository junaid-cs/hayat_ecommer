<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpModel extends Model
{
    protected $table = "otp";
    protected $fillable = ['email', 'code', 'type', 'is_used'];
}
