<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobanteSunat extends Model
{
    use HasFactory;

    protected $table = 'comprobante_sunat';

    protected $fillable = [
        'business_id',
        'location_id',
        'contact_id',
        'numero_doc',
        'name',
        'address',
        'type',
        'invoice_no',
        'ref_no',
        'total',
        'response_sunat',
        'response_nota_sunat',
        'status_sunat',
        'productos',
        'moneda',
        'fecha_emision',
        'fecha_vencimiento',
        'tipo_pago'
    ];
}
