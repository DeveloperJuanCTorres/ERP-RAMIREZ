<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;

class ComprobanteSunatMail extends Mailable
{
    public $pdfData;
    public $xmlData;
    public $comprobante;
    public $nombreBase;

    
    public function __construct($comprobante, $pdfData, $xmlData, $nombreBase)
    {
        $this->comprobante = $comprobante;
        $this->pdfData = $pdfData;
        $this->xmlData = $xmlData;
        $this->nombreBase = $nombreBase;
    }

    public function build()
    {
        return $this->subject('Comprobante Electrónico ' . $this->nombreBase)
            ->view('emails.comprobante')
            ->attachData($this->pdfData, $this->nombreBase . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($this->xmlData, $this->nombreBase . '.xml', [
                'mime' => 'application/xml',
            ]);
    }
}
