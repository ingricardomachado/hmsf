<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\SignedupCondominium;
use App\Mail\WelcomeAdmin;
use App\Models\Condominium;


class SendWelcomeEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $condominium;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->condominium=Condominium::find($user->condominium_id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new WelcomeAdmin($this->user));
        Mail::to('ing.ricardo.machado@gmail.com')->send(new SignedupCondominium($this->condominium));        
    }
}
