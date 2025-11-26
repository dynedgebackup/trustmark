<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BusinessFollowupUnpaids extends Model
{
    use HasFactory;

    protected $fillable = [
        'busn_id',
        'year',
        'followup_date',
        'followup_message'
    ];

    public function apiFollowUpUnpaidEmail($business)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
            'details' => [
                'config_code' => 'TRUSTMARK_UNPAID',
                'project_code' => 'TRUSTMARK_UNPAID',
                'template_data' => [
                    'pic_name' => $business->pic_name,
                    'trustmark_id' => $business->trustmark_id
                ],
                'recipient' => [$business->pic_email],
                'bcc' => [],
                'cc' => [],
            ]
        ]);

        return $response;

        // Log::info('Simulating follow up unpaid email send to ' . $business->pic_email . ' for business ID ' . $business->trustmark_id . ' owner: ' . $business->pic_name);
    }
}
