<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyPart extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'id',
        'business_id',
        'part_id',
        'conductor',
        'dni',
        'h_inicio',
        'h_final',
        'zona_trabajo',
        'combustible',
        'created_at'
    ];
}
