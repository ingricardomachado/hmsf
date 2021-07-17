<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignedupOwner extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The owner instance.
     *
     * @var Condominium
     */
    public $owner;
    public $password;    
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($owner, $password)
    {
        $this->owner = $owner;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.signedup_owner')->subject('Propietario registrado');
    }
}
