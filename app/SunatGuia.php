<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SunatGuia extends Model
{

    protected $fillable = [
        'business_id',
        'location_id',
        'contact_id',
        'serie',
        'numero',
        'cliente',
        'fecha',
        'aceptada',
        'estado_sunat',
        'sunat_description',
        'json_envio',
        'json_respuesta',
        'pdf',
        'xml',
        'cdr',
        'cadena_qr',
    ];

    protected $casts = [
        'aceptada' => 'boolean',
        'fecha' => 'date',
    ];

}
