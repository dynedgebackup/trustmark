<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mandrill;
use TCPDF;
use TCPDF_FONTS;
class Email extends Model
{
    public static function sendMail($type, $data)
    {
        try {
            $mandrill = new Mandrill(config('mail.mandrill_api_key.key'));

            $business = $data['business'] ?? null;
            $user = $data['user'] ?? null;
            $verificationUrl = $data['verificationUrl'] ?? null;
            $email = $data['email'] ?? null;
            $otp = $data['otp'] ?? null;

            // get from-email from unified database
            $service = DB::connection('project1')->table('services')
                ->where('code', 'ECB')
                ->first();

            // Default message structure
            $message = [
                'html' => '',
                'subject' => '',
                'from_email' => $service && isset($service->email) ? $service->email : config('mail.from.address', 'noreply@trustmark.gov.ph'),
                'from_name' => 'E-Commerce Philippines Trustmark',
                'to' => [],
            ];

            // Conditions for different emails
            switch ($type) {
                case 'registration':
                    $message['subject'] = 'Trustmark Application Submission - Reference No. '.$business->trustmark_id;
                    //$message['subject'] = 'Your ECPT Application Has Been Received';
                    $business = DB::table('businesses')->where('id', $business->id)->first();
                    $type_corporations = DB::table('type_corporations')
                        ->where('id', $business->corporation_type)
                        ->first();

                    $busines_fee = DB::table('business_fees')->where('busn_id', $business->id)->get();

                    // ========== Generate PDF ==========
                    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
                    $pdf->SetCreator(PDF_CREATOR);
                    $pdf->SetMargins(10, 10, 10);
                    $pdf->SetAutoPageBreak(true, 10);
                    $pdf->SetPrintHeader(false);
                    $pdf->SetPrintFooter(false);
                    $pdf->AddPage();

                    // watermark
                    $pdf->SetAlpha(0.08);
                    $pdf->Image(public_path('assets/img/trustmark_logo.png'), 35, 47, 140, 200);
                    $pdf->SetAlpha(1);

                    // html load
                    $html = view('business.certificate_statement', compact(
                        'business',
                        'type_corporations',
                        'busines_fee'
                    ))->render();

                    $pdf->writeHTML($html, true, false, true, false, '');

                    // PDF as a string (important!)
                    $pdfString = $pdf->Output('statement_CERTIFICATE.pdf', 'S');

                    // ========== Email Content ==========
                    $message['html'] = View::make('emails.received', compact('business'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    $message['attachments'][] = [
                        'content' => base64_encode($pdfString),
                        'type'    => 'application/pdf',
                        'name'    => 'statement_CERTIFICATE.pdf',
                    ];
                    break;

                case 'adminApproved':
                    $message['subject'] = 'Trustmark Application Approved - Reference No. '.$business->trustmark_id;
                    
                    $message['html'] = View::make('emails.approved', compact('business'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                case 'adminReturned':
                    $reason = $data['reason'] ?? null;
                    $paragraph = $data['paragraph'] ?? null;

                    $message['subject'] = 'Trustmark Application Returned - Reference No. '.$business->trustmark_id;
                    $message['html'] = View::make(
                        'emails.returned',
                        compact('business', 'reason', 'paragraph'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                case 'adminDisapproved':
                    $message['subject'] = $data['subject'] ?? 'ECPT Document Evaluation Result';
                    $message['html'] = View::make('emails.disapproved', compact('business'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                case 'received':
                    $message['subject'] = 'Trustmark Application Submission - Reference No. '.$business->trustmark_id;
                    $message['html'] = View::make('emails.submission_received', compact('business'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                case 'followUpReturn':
                    $message['subject'] = $data['subject'] ?? 'TRUSTMARK FOLLOW UP';
                    
                    $templateName='emails.business_followup_returns';
                    if($data['totalCount']==4){
                        $templateName='emails.final_business_followup_returns';
                    }
                    $message['html'] = View::make($templateName, compact('business'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                case 'followUpUnpaid':
                    $message['subject'] = $data['subject'] ?? 'TRUSTMARK UNPAID';
                    $templateName='emails.business_followup_unpaids';
                    if($data['totalCount']==4){
                        $templateName='emails.final_business_followup_unpaids';
                    }

                    $message['html'] = View::make($templateName, compact('business'))->render();
                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                case 'emailVerification':
                    $message['subject'] = 'TRUSTMARK EMAIL VERIFICATION';
                    $message['html'] = View::make('emails.emailVerification', compact('user', 'verificationUrl'))->render();

                    $message['to'][] = [
                        'email' => $user->email,
                        'name' => $user->name,
                        'type' => 'to',
                    ];
                    break;

                case 'sendOtp':
                    $message['subject'] = 'ECPT Password Recovery OTP';
                    $message['html'] = View::make('emails.otp', compact('email', 'otp', 'user'))->render();

                    $message['to'][] = [
                        'email' => $user->email,
                        'name' => $user->name,
                        'type' => 'to',
                    ];
                    break;

                case 'regenerateTrustmarkId':
                    $message['subject'] = 'Trustmark Application Submission - Reference No. '.$business->trustmark_id;
                    //$message['subject'] = 'Your ECPT Application Has Been Received';
                    $message['html'] = View::make('emails.received', compact('business'))->render();

                    $message['to'][] = [
                        'email' => $business->pic_email,
                        'name' => $business->pic_name,
                        'type' => 'to',
                    ];
                    break;

                    // default:
                    //     $message['subject'] = 'General Notification';
                    //     $message['html'] = "<p>Hello {$data['to_name']},</p><p>This is a general email.</p>";
                    //     break;
            }

            // Send the email and log the result
            $result = $mandrill->messages->send($message);

            // Log the email sending result
            Log::info('Mandrill email sent', [
                'type' => $type,
                'to' => $message['to'],
                'result' => $result,
            ]);

            return [
                'success' => true,
                'result' => $result,
                'error' => null,
                'log' => 'Mandrill email sent',
            ];
        } catch (\Mandrill_Error $e) {
            // Log the error
            Log::error('Mandrill email error', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'result' => null,
                'error' => $e->getMessage(),
                'log' => 'Mandrill email error',
            ];
        }
    }
}
