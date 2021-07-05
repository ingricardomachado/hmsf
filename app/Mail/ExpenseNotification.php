<?php

namespace App\Mail;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpenseNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    public $expense;    
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.expense_notification')
                    ->subject('Pago a Proveedor');
                    //->attach(storage_path('app/'.$this->expense->condominium_id.'/expenses/'.$this->expense->file),['as' => $this->expense->file_name]);
    }
}
