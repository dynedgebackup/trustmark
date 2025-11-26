<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailTokenService;
use Illuminate\Http\Request;

class EmailTokenController extends Controller
{
    protected $emailTokenService;

    public function __construct(EmailTokenService $emailTokenService)
    {
        $this->emailTokenService = $emailTokenService;
    }

    /**
     * Check if email can be sent and get countdown info
     */
    public function checkCountdown(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $countdown = $this->emailTokenService->getCountdownMessage($request->email);

        return response()->json([
            'can_send' => $countdown['can_send'],
            'message' => $countdown['message'],
            'seconds_remaining' => $countdown['seconds_remaining']
        ]);
    }

    /**
     * Get time until next email can be sent
     */
    public function getTimeUntilNext(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $secondsRemaining = $this->emailTokenService->getTimeUntilNextEmail($request->email);

        return response()->json([
            'can_send' => $secondsRemaining === null,
            'seconds_remaining' => $secondsRemaining ?: 0
        ]);
    }
}