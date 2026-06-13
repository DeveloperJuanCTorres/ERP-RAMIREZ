<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SunatGuia extends Model
{

    protected $fillable=[
        'business_id',
        'location_id',
        'serie',
        'numero',
        'contact_id',
        'cliente',
        'fecha',
        'aceptada',
        'sunat_description',
        'json_envio',
        'json_respuesta',
        'pdf',
        'xml',
        'cdr'
    ];

}
