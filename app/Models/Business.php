<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Spatie\Browsershot\Browsershot;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCPDF;
use TCPDF_FONTS;
use DB;
use Carbon\Carbon;

class Business extends Model
{
    use HasFactory;
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'trustmark_id' => $this->trustmark_id,
        ];
    }

    protected $fillable = [
        'id',
        'user_id',
        'corporation_type',
        'reg_num',
        'tin',
        'business_name',
        'franchise',
        'building_no',
        'building_name',
        'block_no',
        'lot_no',
        'street',
        'subdivision',
        'province_id',
        'region_id',
        'municipality_id',
        'barangay_id',
        'zip_code',
        'district',
        'complete_address',
        'docs_business_reg',
        'docs_business_permit',
        'docs_bir_2303',
        'docs_internal_redress',
        'pic_name',
        'pic_ctc_no',
        'pic_email',
        'docs_autorization_form',
        'payment_id',
        'payment_channel',
        'amount',
        'status',
        'qr_code',
        'certificate',
        'trustmark_id',
        'category_id',
        'category_other_description',
        'date_issued',
        'expired_date',
        'url_platform',
        'is_active',
        'requirement_id',
        'requirement_upload',
        'requirement_expired',
        'app_code',
        'tax_year',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'admin_remarks',
        'admin_status',
        'app_status_id',
        'app_canned_id',
        'is_bmbe',
        'evaluator_id',
        'evaluator_assigned_date',
        'bmbe_doc',
        'busn_category_id',
        'busn_valuation_doc',
        'admin_updated_by',
        'admin_updated_at',
        'pic_ctc_no_is_confidential',
        'submit_date'
    ];

    protected $casts = [
        'url_platform' => 'array',
    ];

    public function corporationType()
    {
        return $this->belongsTo(TypeCorporation::class, 'corporation_type');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function requirement()
    {
        return $this->belongsTo(RequirementReps::class, 'requirement_id');
    }

    public function qr($business)
    {
        $qrLink = route('business.qr', $business->id);

        $now = now();
        $timestamp = $now->format('ymdHis') . substr((string) $now->micro, 0, 2);
        $fileName = 'qr_' . $business->id . '_' . $timestamp . '.png';

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrLink)
            ->size(300)
            ->foregroundColor(new Color(13, 45, 156))
            ->build();

        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');

        $uploadDir = "document-upload/qr_code/{$year}/{$month}";
        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }
        $filename = $uploadDir.'/'.$fileName;
        Storage::disk('public')->put($filename, $result->getString());

        return $fileName;
    }
    

    public function generateCertificate($business)
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

        $marginPx = 30;
        $paddingPx = 5;
        $borderThicknessPx = 15;
        $margin = $marginPx * 0.2646;        
        $padding = $paddingPx * 0.2646;
        $borderThickness = $borderThicknessPx * 0.2646;
        $pdf->SetMargins($margin, $margin, $margin);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(true, $margin);
        $pdf->AddPage();
        $pageWidth = $pdf->getPageWidth();
        $pageHeight = $pdf->getPageHeight();
        $x = $margin;
        $y = $margin;
        $w = $pageWidth - 2 * $margin;
        $h = $pageHeight - 2 * $margin;
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth($borderThickness);
        $pdf->Rect(
            $x + $borderThickness / 2,
            $y + $borderThickness / 2,
            $w - $borderThickness,
            $h - $borderThickness
        );
        $contentMargin = $margin + $borderThickness + $padding;
        $pdf->SetMargins($contentMargin, $contentMargin, $contentMargin);
        $pdf->SetAutoPageBreak(true, $contentMargin);
        $montserrat = \TCPDF_FONTS::addTTFfont(public_path('fonts/montserrat/Montserrat-Regular.ttf'), 'TrueTypeUnicode', '', 32);
        $pdf->SetFont($montserrat, '', 10);
        
        $html = view('business.certificate', compact('business'))->render();
        
        $pdf->writeHTML($html, true, false, true, false, '');
        $nameLength = strlen($business->business_name);
        // print_r($nameLength); 
        // exit;
        if ($nameLength > 125) {
            $y = $pdf->getPageHeight() - 50;
        } elseif ($nameLength > 100) {
            $y = $pdf->getPageHeight() - 60;
        } elseif ($nameLength > 70) {
            $y = $pdf->getPageHeight() - 65;
        } elseif ($nameLength > 30) {
            $y = $pdf->getPageHeight() - 70;
        } else {
            $y = $pdf->getPageHeight() - 70;
        }
        
        
        $x = ($pdf->getPageWidth() - 40) / 2;
        $users = DB::table('users')->where('is_primary', 1)->first();
        // print_r($users); 
        // exit;
        
        if(!empty($users)){
            $pdf->Image(public_path('storage/' . $users->profile_photos), $x, $y, 40, 20, '', '', '', true, 300);
            // $pdf->Image(public_path('assets/img/signature_1752930308.png'), $x, $y, 40, 20, '', '', '', true, 300);
            $pdf->SetXY($x, $y + 17);
            $pdf->SetFont($montserrat, '', 12);
            $pdf->Cell(40, 6, 'MA. CRISTINA A. ROQUE', 0, 1, 'C');
            $pdf->SetFont($montserrat, '', 10);
            $pdf->Cell(185, 6, 'Secretary', 0, 1, 'C');
        }else{
            $pdf->SetXY($x, $y + 17);
            $pdf->SetFont($montserrat, '', 12);
            $pdf->Cell(40, 6, 'MA. CRISTINA A. ROQUE', 0, 1, 'C');
            $pdf->SetFont($montserrat, '', 10);
            $pdf->Cell(185, 6, 'Secretary', 0, 1, 'C');
        }
        
        
        $tempPath = storage_path('app/temp_certificate.pdf');
        $pdf->Output($tempPath, 'F');

        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');

        $uploadDir = "document-upload/certificate/{$year}/{$month}";
        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }
        $filename = $uploadDir.'/Trustmark_' . $business->trustmark_id . '.pdf';

        Storage::disk('public')->put($filename, file_get_contents($tempPath));
        @unlink($tempPath);

        return $filename;
    }



    // public function generateCertificate($business)
    // {
    //     $html = view('business.certificate', compact('business'))->render();

    //     $now = now();
    //     $timestamp = $now->format('ymdHis') . substr((string) $now->micro, 0, 2);
    //     $fileName2 = 'certificate_' . $business->id . '_' . $timestamp . '.pdf';

    //     // Generate PDF in temp location first
    //     $tempPath = sys_get_temp_dir() . '/' . $fileName2;

    //     Browsershot::html($html)
    //         ->format('A4')
    //         // ->margins(10, 10, 10, 10)
    //         ->timeout(240)
    //         ->setOption('args', ['--no-sandbox'])
    //         ->savePdf($tempPath);

    //     // Move to storage using Storage facade
    //     Storage::disk('public')->put('document-upload/certificate/' . $fileName2, file_get_contents($tempPath));

    //     // Clean up temp file
    //     unlink($tempPath);

    //     return $fileName2;
    // }

    public function apiSendReceivedEmail($business)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
            'details' => [
                'config_code' => 'TRUSTMARK',
                'project_code' => 'TRUSTMARK',
                'template_data' => [
                    'business_name' => $business->business_name,
                    'trustmark_id' => $business->trustmark_id,
                ],
                'recipient' => [$business->pic_email],
                'bcc' => [],
                'cc' => [],
            ]
        ]);

        return $response;
    }

    public function apiSendApprovedEmail($business)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
            'details' => [
                'config_code' => 'TRUSTMARK_APPROVED',
                'project_code' => 'TRUSTMARK',
                'template_data' => [
                    'business_name' => $business->business_name,
                    'trustmark_id' => $business->trustmark_id,
                ],
                'recipient' => [$business->pic_email],
                'bcc' => [],
                'cc' => [],
            ]
        ]);

        return $response;
    }

    public function apiSendReturnedEmail($business, $reason, $remark, $paragraph)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
            'details' => [
                'config_code' => 'TRUSTMARK_RETURNED',
                'project_code' => 'TRUSTMARK',
                'template_data' => [
                    'business_name' => $business->business_name,
                    'reason' => $reason,
                    'remarks' => $remark,
                    'paragraph' => $paragraph,
                ],
                'recipient' => [$business->pic_email],
                'bcc' => [],
                'cc' => [],
            ]
        ]);

        return $response;
    }

    public function apiSendDisapprovedEmail($business, $reason)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => config('mail.email_api.key'),
            'Content-Type' => 'application/json',
        ])->post(config('mail.email_api.url_async'), [
            'details' => [
                'config_code' => 'TRUSTMARK_DISAPPROVED',
                'project_code' => 'TRUSTMARK',
                'template_data' => [
                    'business_name' => $business->business_name,
                    'reason' => $reason,
                ],
                'recipient' => [$business->pic_email],
                'bcc' => [],
                'cc' => [],
            ]
        ]);

        return $response;
    }

   
}
