<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;

class ComprobanteSunatMail extends Mailable
{
    public $pdfData;
    public $xmlData;
    public $comprobante;

    
    public function __construct($comprobante, $pdfData, $xmlData)
    {
        $this->comprobante = $comprobante;
        $this->pdfData = $pdfData;
        $this->xmlData = $xmlData;
    }

    public function build()
    {
        return $this->subject('Comprobante Electrónico')
            ->view('emails.comprobante')
            ->attachData($this->pdfData, 'Comprobante.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($this->xmlData, 'Comprobante.xml', [
                'mime' => 'application/xml',
            ]);
    }
}
