<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The database connection name for the model.
     * This connects to Project 1's database for SSO authentication
     *
     * @var string
     */
    protected $connection = 'project1';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'username',
        'email',
        'ctc_no',
        'role',
        'email_verified_at',
        'password',
        'remember_token',
        'provider',
        'provider_id',
        'is_active',
        'is_primary',
        'trustmark_admin',
        'trustmark_evaluator',
        'profile_photos',
        'requirement_id',
        'requirement_upload',
        'requirement_expired',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Check if this user was authenticated via SSO
     */
    public function isSsoUser(): bool
    {
        return session('sso_authenticated', false);
    }

    /**
     * Get the SSO token for this user session
     */
    public function getSsoToken(): ?string
    {
        return session('sso_token');
    }

    public function apiSendOTPEmail($email, $otp, $name)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
            'details' => [
                'config_code' => 'TRUSTMARK_PASSWORD_RECOVERY',
                'project_code' => 'TRUSTMARK',
                'template_data' => [
                    'name' => $name,
                    'otp' => $otp,
                ],
                'recipient' => [$email],
                'bcc' => [],
                'cc' => [],
            ]
        ]);

        return $response;
    }

    public function apiSendEmailVerify($email, $url, $name)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
                    'details' => [
                        'config_code' => 'TRUSTMARK_VERIFY',
                        'project_code' => 'TRUSTMARK',
                        'template_data' => [
                            'verificationUrl' => $url,
                            'name' => $name
                        ],
                        'recipient' => [$email],
                        'bcc' => [],
                        'cc' => [],
                    ]
                ]);

        return $response;
    }
}
