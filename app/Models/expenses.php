<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expenses extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'donation_type_id','amount','date','paid'
    ];
}
