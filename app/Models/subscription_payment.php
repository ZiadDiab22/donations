<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subscription_payment extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'amount',
        'date'
    ];
}
