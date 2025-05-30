<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanInternals extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'id',
        'business_id',
        'user_id',
        'amount',
        'type',
        'time',
        'tax',
        'total_pay',
        'created_by',
        'created_at'
    ];
}
