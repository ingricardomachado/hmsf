<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignedOperation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The owner instance.
     *
     * @var Condominium
     */
    public $operation;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($operation)
    {
        $this->operation = $operation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.assigned_operation')->subject('Operacion Asignada');
    }
}
