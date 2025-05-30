<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPayments extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'loan_payments';

    protected $fillable = [
        'id',
        'loan_id',
        'date_pay',
        'amount_pay',
        'method_pay',
        'observation'
    ];

    public function loan()
    {
        return $this->belongsTo(LoanInternals::class);
    }
}
