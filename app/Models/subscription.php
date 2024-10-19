<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subscription extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'all_amount',
        'paid_amount',
        'remaining_amount',
        'start_date',
        'end_date',
        'duration'
    ];
}
