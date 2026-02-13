<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;

class ComprobanteSunatMail extends Mailable
{
    public $comprobante;
    public $pdfPath;
    public $xmlPath;

    
    public function __construct($comprobante, $pdfPath, $xmlPath)
    {
        $this->comprobante = $comprobante;
        $this->pdfPath = $pdfPath;
        $this->xmlPath = $xmlPath;
    }

    public function build()
    {
        return $this->subject('Comprobante Electrónico')
            ->view('emails.comprobante')
            ->attach($this->pdfPath)
            ->attach($this->xmlPath);
    }
}
