<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Part extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'id',
        'business_id',
        'proveedor_id',
        'cliente_id',
        'product_id',
        'observations',
        'created_at'
        
    ];
}
