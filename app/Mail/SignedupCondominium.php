<?php

namespace App\Mail;

use App\Models\Condominium;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignedupCondominium extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The condominium instance.
     *
     * @var Condominium
     */
    public $condominium;    
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Condominium $condominium)
    {
        $this->condominium = $condominium;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.signedup_condominium')->subject('Condominio registrado');
    }
}
