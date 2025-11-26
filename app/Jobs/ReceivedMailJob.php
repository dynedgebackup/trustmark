<?php

namespace App\Jobs;

use App\Mail\ReceivedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ReceivedMailJob implements ShouldQueue
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
        try {
            Mail::to($this->business->pic_email)->send(new ReceivedMail($this->business));

            Log::info('ReceivedMail sent successfully', [
                'to' => $this->business->pic_email,
                'business_id' => $this->business->id ?? null,
            ]);
        } catch (TransportExceptionInterface $e) {
            Log::error('Email failed to send via SMTP', [
                'to' => $this->business->pic_email,
                'business_id' => $this->business->id ?? null,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            // Optional: rethrow if you want Laravel to mark the job as failed
            throw $e;
        }
    }
}
