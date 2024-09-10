<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class donation extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id','donation_type_id','amount','date'
    ];
}
