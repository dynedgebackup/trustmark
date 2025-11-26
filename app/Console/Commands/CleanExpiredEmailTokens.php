<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailTokenService;

class CleanExpiredEmailTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-tokens:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired email tokens from the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EmailTokenService $emailTokenService)
    {
        $this->info('Cleaning expired email tokens...');
        
        $deletedCount = $emailTokenService->cleanExpiredTokens();
        
        $this->info("Cleaned {$deletedCount} expired email tokens.");
        
        return 0;
    }
}