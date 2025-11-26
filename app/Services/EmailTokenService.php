<?php

namespace App\Services;

use App\Models\EmailToken;
use Carbon\Carbon;

class EmailTokenService
{
    /**
     * Check if email can be sent (no active tokens exist)
     */
    public function canSendEmail(string $email): bool
    {
        return EmailToken::canSendEmail($email);
    }

    /**
     * Get remaining time before next email can be sent
     */
    public function getTimeUntilNextEmail(string $email): ?int
    {
        $activeToken = EmailToken::where('email', $email)
            ->where('is_taken', false)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'desc')
            ->first();

        if (!$activeToken) {
            return null;
        }

        return now()->diffInSeconds($activeToken->expires_at);
    }

    /**
     * Create email token and return it
     */
    public function createEmailToken(string $email): EmailToken
    {
        return EmailToken::createForEmail($email);
    }

    /**
     * Validate and consume token
     */
    public function validateAndConsumeToken(string $token): bool
    {
        $emailToken = EmailToken::findValidToken($token);
        
        if (!$emailToken || !$emailToken->isValid()) {
            return false;
        }

        return $emailToken->markAsTaken();
    }

    /**
     * Get countdown message for UI
     */
    public function getCountdownMessage(string $email, string $baseMessage = 'Please verify your email before logging-in.'): array
    {
        $secondsRemaining = $this->getTimeUntilNextEmail($email);
        
        if ($secondsRemaining === null) {
            return [
                'can_send' => true,
                'message' => $baseMessage,
                'seconds_remaining' => 0
            ];
        }

        $minutesRemaining = ceil($secondsRemaining / 60);
        
        return [
            'can_send' => false,
            'message' => $baseMessage . "\nNew verification will be sent after {$minutesRemaining} minutes.",
            'seconds_remaining' => $secondsRemaining
        ];
    }

    /**
     * Clean expired tokens (can be called via scheduled command)
     */
    public function cleanExpiredTokens(): int
    {
        return EmailToken::where('expires_at', '<', now())->delete();
    }
}