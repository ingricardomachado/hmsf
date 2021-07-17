<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\FreeEmail;


class SendFreeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $property;
    public $subject;
    public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($property, $subject, $body)
    {
        $this->property = $property;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->property->user->email)->send(new FreeEmail($this->property, $this->subject, $this->body));
    }
}
