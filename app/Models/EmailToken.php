<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailToken extends Model
{
    protected $fillable = ['email', 'token', 'is_taken'];

    protected $casts = [
        'is_taken' => 'boolean',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a unique token
     */
    public static function generateToken(): string
    {
        return hash('sha256', Str::random(64) . microtime(true));
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    /**
     * Check if token is valid (not taken and not expired)
     */
    public function isValid(): bool
    {
        return !$this->is_taken && !$this->isExpired();
    }

    /**
     * Mark token as taken
     */
    public function markAsTaken(): bool
    {
        return $this->update(['is_taken' => true]);
    }

    /**
     * Check if user can request new email (no valid tokens exist)
     */
    public static function canSendEmail(string $email): bool
    {
        return !self::where('email', $email)
            ->where('is_taken', false)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Create a new email token for user
     */
    public static function createForEmail(string $email): self
    {
        return self::create([
            'email' => $email,
            'token' => self::generateToken(),
            'is_taken' => false,
        ]);
    }

    /**
     * Find valid token
     */
    public static function findValidToken(string $token): ?self
    {
        return self::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }
}