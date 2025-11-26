<?php

namespace App\Jobs;

use App\Mail\ApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovedMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $business;

    /**
     * Create a new job instance.
     */
    public function __construct($business)
    {
        $this->business = $business;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->business->pic_email)->send(new ApprovedMail($this->business));
    }
}
