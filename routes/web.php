<?php

use App\Http\Controllers\AdminBarangayController;
use App\Http\Controllers\AdminMunicipalityController;
use App\Http\Controllers\ApplicationStatusCannedMessageController;
use App\Http\Controllers\ArchivedApplicationsReportController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\BusinessCategoryController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\EvaluatorController;
use App\Http\Controllers\FeesDescriptionController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MenuGroupController;
use App\Http\Controllers\MenuModuleController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ProvincesController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\RequirementRepsController;
use App\Http\Controllers\ScheduleFeesController;
use App\Http\Controllers\OnlinePlatformsControlle;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\ReturnedApplicationsReportController;
use App\Http\Controllers\EvaluatorKpiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', function () {
    // Optionally cache it for performance
    $isEnabled = DB::table('settings')->where('name', 'is_enable_metadata')->value('value');
    if ($isEnabled) {
        $content = "User-agent: *\nAllow: /";
    } else {
        $content = "User-agent: *\nDisallow: /";
    }

    return response($content, 200)
        ->header('Content-Type', 'text/plain');
});
// SSO Authentication Routes
Route::get('sso/authenticate', [SsoController::class, 'authenticate'])->name('sso.authenticate');
Route::get('/sso/check-session', [SsoController::class, 'checkSession'])->name('sso.check');
Route::post('/sso/logout', [SsoController::class, 'logout'])->name('sso.logout');
Route::post('/sso/force-logout', [SsoController::class, 'forceLogout'])->name('sso.force-logout');
Route::post('sso/verify-token', [SsoController::class, 'verifyToken']);
Route::post('/sso/logout-broadcast', [SsoController::class, 'broadcastLogout']);
Route::get('/redirect-to-app2', [SsoController::class, 'redirectToApp2'])
    ->name('sso.redirect.to.app2');
// ----------- Email Verification ------------
Route::get('/email/verify/{id}', function (Request $request, $encryptedId) {
    // First try to decrypt the user ID to check if they're already verified
    try {
        $userId = Crypt::decryptString($encryptedId);
        $user = User::find($userId);
        
        // If user exists and is already verified, show success page regardless of signature
        if ($user && $user->email_verified_at) {
            \Log::info('Email verification: User already verified', [
                'user_id' => $user->id,
                'email' => $user->email,
                'verified_at' => $user->email_verified_at
            ]);
            
            return view('auth.403-verification-error', [
                'isSuccess' => true,
                'message' => 'Email already verified',
                'user' => $user
            ]);
        }
    } catch (\Exception $e) {
        \Log::error('Email verification: Failed to decrypt user ID', [
            'encrypted_id' => $encryptedId,
            'error' => $e->getMessage()
        ]);
    }

    // Now check signature for new verifications
    if (! $request->hasValidSignature()) {
        \Log::warning('Email verification: Invalid signature', [
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ]);
        
        // Show our custom 403 page instead of generic abort
        return view('auth.403-verification-error', [
            'message' => 'Invalid or expired verification link',
            'showResendButton' => true,
            'showEmailInput' => true, // Show email input since we don't know the user
            'user' => (object)['email' => ''] // Empty email so user can enter it
        ]);
    }

    try {
        $userId = Crypt::decryptString($encryptedId);
    } catch (\Exception $e) {
        // Show our custom 403 page instead of generic abort
        return view('auth.403-verification-error', [
            'message' => 'Invalid verification link',
            'showResendButton' => true,
            'showEmailInput' => true, // Show email input since decryption failed
            'user' => (object)['email' => '']
        ]);
    }

    $user = User::findOrFail($userId);

    // Validate email token if provided
    if ($request->has('token')) {
        $emailTokenService = app(\App\Services\EmailTokenService::class);
        if (!$emailTokenService->validateAndConsumeToken($request->token)) {
            // Show our custom 403 page instead of generic abort
            return view('auth.403-verification-error', [
                'message' => 'Invalid or expired verification token',
                'showResendButton' => true,
                'user' => $user
            ]);
        }
    } else {
        // No token provided - show 403 page
        return view('auth.403-verification-error', [
            'message' => 'Invalid or expired verification token',
            'showResendButton' => true,
            'user' => $user
        ]);
    }

    // Check if email is already verified - show success page
    if ($user->email_verified_at) {
        return view('auth.403-verification-error', [
            'isSuccess' => true,
            'message' => 'Email already verified',
            'user' => $user
        ]);
    }

    // If not verified, mark as verified now
    $user->email_verified_at = now();
    $user->save();

    // Show success page for newly verified email
    return view('auth.403-verification-error', [
        'isSuccess' => true,
        'message' => 'Email successfully verified',
        'user' => $user
    ]);
})->name('verification.verify'); // Removed ->middleware('signed') so we can handle signature manually

// Simple resend verification email route
Route::post('/email/resend-verification', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    
    $user = User::where('email', $request->email)->first();
    
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    }

    if ($user->email_verified_at) {
        return response()->json(['success' => false, 'message' => 'Email already verified.'], 400);
    }

    // Check if user can send email (simple version)
    $emailTokenService = app(\App\Services\EmailTokenService::class);
    if (!$emailTokenService->canSendEmail($user->email)) {
        return response()->json([
            'success' => false, 
            'message' => 'Please wait before requesting another email.'
        ], 429);
    }

    // Create email token and send email
    $token = $emailTokenService->createEmailToken($user->email);
    
    // Send email using Mandrill Email service
    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(10), // 10 minutes expiration
        [
            'id' => Crypt::encryptString($user->id),
            'token' => $token->token
        ]
    );
    
    // Log the email sending attempt
    \Log::info('Attempting to send verification email via Mandrill', [
        'email' => $user->email,
        'user_id' => $user->id,
        'token' => $token->token,
        'url' => $url
    ]);
    
    try {
        // Use the existing Mandrill Email service
        $emailResult = \App\Models\Email::sendMail('emailVerification', [
            'user' => $user,
            'verificationUrl' => $url
        ]);
        
        // Log the email result
        \Log::info('Mandrill Email service response', [
            'email' => $user->email,
            'success' => $emailResult['success'],
            'result' => $emailResult['result'] ?? null,
            'error' => $emailResult['error'] ?? null
        ]);
        
        // Check if email sending was successful
        if ($emailResult['success']) {
            \Log::info('Verification email sent successfully via Mandrill', ['email' => $user->email]);
            return response()->json([
                'success' => true, 
                'message' => 'Verification email sent successfully!'
            ]);
        } else {
            \Log::error('Mandrill email service failed', [
                'email' => $user->email,
                'error' => $emailResult['error']
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send email. Please try again.'
            ], 500);
        }
    } catch (\Exception $e) {
        \Log::error('Exception while sending verification email via Mandrill', [
            'email' => $user->email,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false, 
            'message' => 'Failed to send email. Please try again.'
        ], 500);
    }
})->name('verification.resend-simple');

// TEST ROUTE for 403 error page design
Route::get('/test-403-design', function () {
    return view('auth.403-verification-error', [
        'message' => 'Invalid or expired verification token',
        'showResendButton' => true,
        'user' => (object)['email' => 'test@example.com']
    ]);
})->name('test.403.design');

// TEST ROUTE - Generate fresh verification URL with 5-second expiration
Route::get('/test-generate-url/{email?}', function ($email = 'test@example.com') {
    $emailTokenService = app(\App\Services\EmailTokenService::class);
    
    // Create a test user object
    $testUser = (object)[
        'id' => 999,
        'email' => $email,
        'name' => 'Test User'
    ];
    
    // Create email token
    $token = $emailTokenService->createEmailToken($email);
    
    // Generate fresh URL with 5-second expiration
    $encryptedId = encrypt($testUser->id);
    $signedUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(10), // 10 minutes expiration
        ['id' => $encryptedId, 'token' => $token->token]
    );
    
    return response()->json([
        'message' => 'Fresh verification URL generated (expires in 5 seconds)',
        'url' => $signedUrl,
        'instructions' => [
            'Copy the URL above',
            'Wait 6+ seconds',
            'Then paste and visit the URL to test expired signature'
        ],
        'immediate_test' => 'Click the URL immediately to test valid verification'
    ]);
})->name('test.generate-url');

Route::any('/payment/webhook', [WebhookController::class, 'handle']);
Route::any('/updatePaymentResponse', [BusinessController::class, 'updatePaymentResponse'])->name('business.updatePaymentResponse');
Route::any('/payment/generateCertificates', [WebhookController::class, 'generateCertificates']);
Route::any('/payment/update-payment-status', [WebhookController::class, 'updatePaymentStatus']);

// ----------- Database Search ------------

Route::get('/db-search', function () {
    return view('search');
})->name('home');

Route::post('/search', [BusinessController::class, 'search'])->name('business.search');
// ----------- Customer Register ------------
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register.create');
Route::post('/register/store', [RegisteredUserController::class, 'store'])->name('register.store');

// ----------- Forgot Password ------------
Route::get('/forgot-password', [AuthenticatedSessionController::class, 'forgot_password'])->name('login.forgot_password');
Route::post('/forgot-password/otp', [UserController::class, 'sendOtp'])->name('login.otp');
Route::post('/forgot-password/verify', [UserController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/forgot-password/verify', function (Request $request) {
    // Handle GET requests to prevent 405 errors
    $email = $request->query('email');
    if ($email) {
        return redirect()->route('login.otp.form', ['email' => $email]);
    }

    return redirect()->route('login.forgot_password');
})->name('verify.otp.get');
Route::post('/forgot-password/reset', [UserController::class, 'resetPassword'])->name('password.reset');
Route::get('/forgot-password/otp', function (Request $request) {
    $email = $request->query('email');
    $user = \App\Models\User::where('email', $email)->first();

    return view('auth.verify_otp', compact('user'));
})->name('login.otp.form');

// ----------- Login ------------
Route::get('/login', [AuthenticatedSessionController::class, 'login'])->name('login');
Route::post('/auth', [AuthenticatedSessionController::class, 'auth'])->name('auth');
// Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');

// ----------- Social Auth ------------
Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider']);
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

// ----------- QR Route ------------
Route::get('/business/qr/{id}', [BusinessController::class, 'showQr'])->name('business.qr');

Route::get('/business/{id}', [BusinessController::class, 'downloadCertificate'])
    ->name('business.download_certificate');

Route::middleware(['web'])->group(function () {
    Route::get('/logout', function (Request $request) {
        // Check if this is an SSO user
        if (session('sso_authenticated')) {
            return app(App\Http\Controllers\SsoController::class)->logout($request);
        } else {
            return app(App\Http\Controllers\AuthenticatedSessionController::class)->logout($request);
        }
    })->name('logout');
});

// cronjob
Route::any('/setting/cron-job/follow-up-return-application', [CronJobController::class, 'followUpReturnApplication'])->name('cron-job.follow-up-return-application');
Route::any('/setting/cron-job/follow-up-unpaid', [CronJobController::class, 'followUpUnpaid'])->name('cron-job.follow-up-unpaid');
Route::any('/setting/cron-job/archive-follow-up-return', [CronJobController::class, 'archiveFollowUpReturn'])->name('cron-job.archive-follow-up-return');
Route::any('/setting/cron-job/archive-follow-up-unpaid', [CronJobController::class, 'archiveFollowUpUnpaid'])->name('cron-job.archive-follow-up-unpaid');
Route::any('/setting/cron-job/delete-draft', [CronJobController::class, 'deleteDraft'])->name('cron-job.delete-draft');
Route::get('/business-authorized-download/{id}', [BusinessController::class, 'download_authorized'])->name('business.download_authorized');
Route::get('/business/download-business-registration/{id}', [BusinessController::class, 'download_business_registration'])->name('business.download_business_registration');
Route::get('/business/download_bir_2303/{id}', [BusinessController::class, 'download_bir_2303'])->name('business.download_bir_2303');
Route::get('/business/download_AdditionalDocuments/{id}', [BusinessController::class, 'download_AdditionalDocuments'])->name('business.download_AdditionalDocuments');
Route::get('/business/download_internal_redress/{id}', [BusinessController::class, 'download_internal_redress'])->name('business.download_internal_redress');
Route::get('/business/download_business_document/{id}/{type}', [BusinessController::class, 'download_business_document'])->name('business.download_business_document');
Route::get('/business/download_bmbe_doc/{id}', [BusinessController::class, 'download_bmbe_doc'])->name('business.download_bmbe_doc');
Route::get('/business/download_busn_valuation_doc/{id}', [BusinessController::class, 'download_busn_valuation_doc'])->name('business.download_busn_valuation_doc');
Route::get('/download/internal-redress-template', [BusinessController::class, 'internal_redress_template'])->name('internal.redress.download');
Route::get('/authorized-download/{id}', [UserController::class, 'download_authorized'])->name('profile.download_authorized');
Route::get('/get-province/{reg_no}', [ProvinceController::class, 'getProvinces']);
Route::delete('/documents/{id}', [BusinessController::class, 'AdditionalPermitdestroy'])->name('AdditionalPermitd.destroy');
Route::get('/get-municipalities/{prov_no}', [MunicipalityController::class, 'getMunicipalities']);
Route::get('/get-barangays/{regionId}/{provinceId}/{municipalityId}', [BarangayController::class, 'getBarangays']);
Route::get('/get-reasons/{status_id}', [ApplicationStatusCannedMessageController::class, 'getReasons']);
Route::post('/get-platform-details', [BusinessController::class, 'getPlatformDetails'])->name('platform.details');
Route::get('/business_internal_redress/{id}/certificate', [BusinessController::class, 'generateInternalRedCertificate'])
     ->name('business.certificate');
Route::get('/generateStatmentOfAccutCertificate/{id}/certificate', [BusinessController::class, 'generateStatmentOfAccutCertificate'])
->name('business.generateStatmentOfAccutCertificate');
Route::post('/business-performance', [BusinessController::class, 'performance'])->name('business.performance');

Route::any('/irm-save', [BusinessController::class, 'saveirm'])->name('irm.save');

Route::middleware(['auth','role.access'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/adminapp/getList', [DashboardController::class, 'getList'])->name('adminapp.getList');

    // ----------- Business ------------
    Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
    Route::get('/application/getList', [BusinessController::class, 'getList'])->name('business.getList');
    Route::post('/application/savelogview', [BusinessController::class, 'savelogview'])->name('business.savelogview');

    // save draft when click terms
    // Route::post('/business/auto-store', [BusinessController::class, 'auto_store'])->name('business.auto_store');
    // Route::get('/business/create/{business_id}', [BusinessController::class, 'create'])->name('business.create');

    // save draft when submit first form
    Route::get('/businesss/store', [BusinessController::class, 'auto_store'])->name('business.auto_store');
    Route::get('/businesss/create/{business_id}', [BusinessController::class, 'create'])->name('business.create');

    Route::post('/business/save-corporation', [BusinessController::class, 'save_corporation'])->name('business.save_corporation');
    Route::post('/business/save-details', [BusinessController::class, 'save_detail'])->name('business.save_detail');
    Route::post('/business/save_document', [BusinessController::class, 'save_document'])->name('business.save_document');
    Route::post('/business/submit_form', [BusinessController::class, 'submit_form'])->name('business.submit_form');
    Route::get('/business/view/{id}', [BusinessController::class, 'view'])->name('business.view');
    Route::get('/business/disapproved-view/{id}', [BusinessController::class, 'disapproved_view'])->name('business.disapproved_view');
    Route::post('/business/confidential', [BusinessController::class, 'confidential'])->name('business.confidential');
    Route::get('/business/edit/{id}', [BusinessController::class, 'edit'])->name('business.edit');
    Route::put('/business/admin-update/{id}', [BusinessController::class, 'admin_update'])->name('business.admin_update');
    Route::put('/business/update/{id}', [BusinessController::class, 'update'])->name('business.update');
    
    Route::any('/business/updateEditOnly/{id}', [BusinessController::class, 'updateEditOnly'])->name('business.updateEditOnly');
    Route::any('/business/updateEditOnly2/{id}', [BusinessController::class, 'updateEditOnly2'])->name('business.updateEditOnly2');
    Route::any('/business/updateEditOnly3/{id}', [BusinessController::class, 'updateEditOnly3'])->name('business.updateEditOnly3');
    Route::any('/business/updateEditOnly4/{id}', [BusinessController::class, 'updateEditOnly4'])->name('business.updateEditOnly4');
    Route::any('/business/updateEditMail/{id}', [BusinessController::class, 'updateEditMail'])->name('business.updateEditMail');
    Route::delete('/business/{id}', [BusinessController::class, 'destroy'])->name('business.destroy');
    Route::post('/business-draft/{id}', [BusinessController::class, 'destroy'])->name('business.destroydraft');
    Route::post('/check-tin', [BusinessController::class, 'check_tin_num'])->name('check.tin');
    
    
    Route::get('/business/check-records/{id}', [BusinessController::class, 'getCheckRecords'])->name('business.check-records');
    Route::get('/business/check-business-name-records/{id}', [BusinessController::class, 'getCheckRecordBusinessName'])->name('business.check-business-name-records');
    Route::get('/business/check-business-Registration-records/{id}', [BusinessController::class, 'getCheckRecordBusinessRegistration'])->name('business.check-business-Registration-records');
    Route::get('/audit-logs', [BusinessController::class, 'getAuditLogsList'])->name('audit.logs');
    Route::get('/getFollowupEmailList', [BusinessController::class, 'getFollowupEmailList'])->name('getFollowupEmailList');
    Route::post('/userlogs-view', [BusinessController::class, 'submit_userlogs'])->name('business.submit_userlogs');
    Route::post('/userlogs-downloadQR', [BusinessController::class, 'submit_downloadQR'])->name('business.submit_downloadQR');
    Route::post('/userlogs-downloadCert', [BusinessController::class, 'submit_downloadCert'])->name('business.downloadCert');
    Route::post('/userlogs-regenerateCert', [BusinessController::class, 'submit_regenerateCert'])->name('business.submit_regenerateCert');
    // regenerate and resend email for trustmark_id
    Route::get('/business/regenerate-trustmark/{id}', [BusinessController::class, 'regenerateTrustmark'])->name('business.regenerate-trustmark');

    // ----------- Authorized Representative ------------
    Route::get('/authorized', [RequirementRepsController::class, 'index'])->name('requirement.index');
    Route::post('/authorized/store', [RequirementRepsController::class, 'store'])->name('requirements.store');
    Route::put('/authorized/update/{id}', [RequirementRepsController::class, 'update'])->name('requrement.update');
    
    Route::get('/authorized/getList', [RequirementRepsController::class, 'getList'])->name('authorized.getList');

    // ----------- Admin Business ------------
    Route::get('/business-app', [BusinessController::class, 'businessapp'])->name('business.business-app');

    Route::get('/list-under', [BusinessController::class, 'list_under_evaluation'])->name('business.under-evaluation');
    Route::get('/list-on-hold', [BusinessController::class, 'list_on_hold'])->name('business.list_on_hold');
    Route::get('/get-list-under', [BusinessController::class, 'getlistunderevalution'])->name('business.get-list-under');
    Route::get('/get-list-onhold', [BusinessController::class, 'getlistOnhold'])->name('business.getlistOnhold');
    Route::get('/list-approved', [BusinessController::class, 'list_approved'])->name('business.list-approved');
    Route::get('/get-list-approved', [BusinessController::class, 'getlistapproved'])->name('business.get-list-approved');

    Route::get('/list-paid', [BusinessController::class, 'list_paid'])->name('business.list-paid');
    Route::get('/get-list-paid', [BusinessController::class, 'getlistpaid'])->name('business.get-list-paid');

    Route::get('/list-returned', [BusinessController::class, 'list_returned'])->name('business.list-returned');
    Route::get('/get-list-returned', [BusinessController::class, 'getlistreturned'])->name('business.get-list-returned');

    Route::get('/list-disapproved', [BusinessController::class, 'list_disapproved'])->name('business.list-disapproved');
    Route::get('/get-list-disapproved', [BusinessController::class, 'getlistdisapproved'])->name('business.get-list-disapproved');

    Route::get('/my-task-list', [BusinessController::class, 'mytasklist'])->name('business.mytasklist');
    Route::get('/getmy-task-list', [BusinessController::class, 'getmytasklist'])->name('business.getmytasklist');
    Route::post('/my-task-list-delete/{id}', [BusinessController::class, 'mytasklistdestroy'])->name('business.mytasklistdestroy');
    Route::post('/update-bulkassigment', [BusinessController::class, 'bulkassigment'])->name('business.bulkassigment');
    Route::get('/list-draft', [BusinessController::class, 'list_draft'])->name('business.draft');
    Route::get('/get-list-draft', [BusinessController::class, 'getlistdraft'])->name('business.get-list-draft');
    Route::post('/update-compliance-status', [BusinessController::class, 'updateComplianceStatus'])
    ->name('business_compliance.update');

    Route::get('/business-compliance/get-remarks', [BusinessController::class, 'getRemarks'])->name('business_compliance.getRemarks');
    Route::get('/business-compliance/get-remarksHistory', [BusinessController::class, 'getRemarksHistory'])->name('business_compliance.getRemarksHistory');
    Route::post('/business-compliance/update-remarks', [BusinessController::class, 'updateRemarks'])
    ->name('business_compliance.updateRemarks');
    Route::get('/business/{id}/qr-png', [BusinessController::class, 'downloadQrPng'])->name('business.qr_png');
    Route::get('/tabs/corporations/{id}', [BusinessController::class, 'tabCorporations'])->name('tabs.tabCorporations');
    Route::get('/tabs/document/{id}', [BusinessController::class, 'tabDocument'])->name('tabs.tabDocument');
    // ----------- Payment ------------
    Route::get('/payment-method/{business_id}', [BusinessController::class, 'paymentMothod'])->name('business.paymentMethod');
    Route::get('/addpayment', [BusinessController::class, 'addpaymentayment'])->name('business.addpaymentayment');
    Route::get('/payment-view-page/{tid}', [BusinessController::class, 'displayPaymentPage'])->name('business.displayPaymentPage');
    Route::get('/check-payment-response', [BusinessController::class, 'checkPaymentResponse'])->name('business.checkPaymentResponse');
    Route::post('/business/payment', [BusinessController::class, 'save_payment'])->name('business.save_payment');  // testing purpose, need to remove later
    Route::any('/business/payment2', [BusinessController::class, 'save_payment2'])->name('business.payment.save');
    Route::get('/business/{id}/Re-certificate', [BusinessController::class, 'certReGenerate'])
        ->name('business.certReGenerate');
    Route::get('/business/{id}/urls', [BusinessController::class, 'getUrls'])->name('business.getUrls');
    Route::post('/business/{id}/urls', [BusinessController::class, 'updateUrls'])->name('business.updateUrls');
    Route::post('/business/update-business-information/{id}', [BusinessController::class, 'updateBusinessInformation'])->name('business.updateBusinessInformation');
    Route::post('/business/update-business-AuthorizedRepresentative/{id}', [BusinessController::class, 'updateAuthorizedRepresentative'])->name('business.updateAuthorizedRepresentative');
    Route::get('/business/barangaysearch', [BusinessController::class, 'barangaysearch'])->name('business.barangaysearch');
    Route::get('/business/eveluatorsearch', [BusinessController::class, 'eveluatorsearch'])->name('business.eveluatorsearch');
    Route::post('/business/update-business-businessAddress/{id}', [BusinessController::class, 'updatebusinessAddress'])->name('business.updatebusinessAddress');
    Route::post('/business/update-business-attachments/{id}', [BusinessController::class, 'save_documentattachments'])->name('business.save_documentattachments');
    Route::post('/business/update-business-AdditionalPermitsstore/{id}', [BusinessController::class, 'AdditionalPermitsstoreView'])->name('business.AdditionalPermitsstore');
    Route::post('/business/assign-evaluator', [BusinessController::class, 'assignEvaluator'])
        ->name('business.assignEvaluator');
    Route::get('/monthly-pending-summary', [BusinessController::class, 'getMonthlyPendingSummary'])->name('monthly.pending.summary');
    Route::post('/monthly-pending-summary-Evaluator_id', [BusinessController::class, 'getMonthlyPendingSummaryEvaluator_id'])->name('monthly.pending.summaryEvaluator_id');
    
    // ----------- Profile ------------
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.view');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/view/{id}', [UserController::class, 'admin_profile'])->name('user.view');
    Route::put('/profile/applicant-update/{id}', [UserController::class, 'applicant_update'])->name('profile.applicant_update');
    Route::put('/profile/admin-update/{id}', [UserController::class, 'admin_update'])->name('profile.admin_update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/permissions/save', [UserController::class, 'savePermission'])->name('permissions.save');

    // ----------- scheduleFees ------------
    Route::post('AppcodeAjaxList', [ScheduleFeesController::class, 'AppcodeAjaxList']);
    Route::post('feesAjaxList', [ScheduleFeesController::class, 'feesAjaxList']);
    Route::get('/master-data/scheduleFees', [ScheduleFeesController::class, 'index'])->name('scheduleFees.index');
    Route::get('/master-data/scheduleFees/getList', [ScheduleFeesController::class, 'getList'])->name('scheduleFees.getList');
    Route::get('/master-data/scheduleFees/create', [ScheduleFeesController::class, 'create'])->name('scheduleFees.create');
    Route::any('/master-data/scheduleFees/store', [ScheduleFeesController::class, 'store'])->name('scheduleFees.store');
    Route::get('/master-data/scheduleFees/edit/{id}', [ScheduleFeesController::class, 'edit'])->name('scheduleFees.edit');
    Route::post('/master-data/scheduleFees/update/{id}', [ScheduleFeesController::class, 'update'])->name('scheduleFees_update');
    Route::get('/master-data/scheduleFees/destroy/{id}', [ScheduleFeesController::class, 'destroy'])->name('scheduleFees_destroy');
    Route::post('scheduleFeeslist', [ScheduleFeesController::class, 'getivisionDataList'])->name('scheduleFees.division.list');
    Route::post('/master-data/scheduleFees/ActiveInactive', [ScheduleFeesController::class, 'ActiveInactive'])->name('scheduleFees.ActiveInactive');

    // ----------- businessCategory ------------
    Route::get('/master-data/businessCategory', [BusinessCategoryController::class, 'index'])->name('businessCategory.index');
    Route::get('/master-data/businessCategory/getList', [BusinessCategoryController::class, 'getList'])->name('businessCategory.getList');
    Route::any('/master-data/businessCategory/store', [BusinessCategoryController::class, 'store'])->name('businessCategory.store');
    Route::post('/master-data/businessCategory/ActiveInactive', [BusinessCategoryController::class, 'ActiveInactive'])->name('businessCategory.ActiveInactive');

    // ----------- feesDescription ------------
    Route::get('/master-data/feesDescription', [FeesDescriptionController::class, 'index'])->name('feesDescription.index');
    Route::get('/master-data/feesDescription/getList', [FeesDescriptionController::class, 'getList'])->name('feesDescription.getList');
    Route::any('/master-data/feesDescription/store', [FeesDescriptionController::class, 'store'])->name('feesDescription.store');
    Route::post('/master-data/feesDescription/ActiveInactive', [FeesDescriptionController::class, 'ActiveInactive'])->name('feesDescription.ActiveInactive');

    // ----------- Online Platform ------------
    Route::get('/master-data/onlineplatforms', [OnlinePlatformsControlle::class, 'index'])->name('onlineplatforms.index');
    Route::get('/master-data/onlineplatforms/getList', [OnlinePlatformsControlle::class, 'getList'])->name('onlineplatforms.getList');
    Route::get('/master-data/onlineplatforms/create', [OnlinePlatformsControlle::class, 'create'])->name('onlineplatforms.create');
    Route::any('/master-data/onlineplatforms/store', [OnlinePlatformsControlle::class, 'store'])->name('onlineplatforms.store');
    Route::get('/master-data/onlineplatforms/edit/{id}', [OnlinePlatformsControlle::class, 'edit'])->name('onlineplatforms.edit');
    Route::post('/master-data/onlineplatforms/update/{id}', [OnlinePlatformsControlle::class, 'update'])->name('onlineplatforms_update');
    Route::get('/master-data/onlineplatforms/destroy/{id}', [OnlinePlatformsControlle::class, 'destroy'])->name('onlineplatformsdestroy');
    Route::post('/master-data/onlineplatforms/ActiveInactive', [OnlinePlatformsControlle::class, 'ActiveInactive'])->name('onlineplatforms.ActiveInactive');

    // ----------- Application Status Canned Message ------------
    Route::post('applicationStatusAjaxList', [ApplicationStatusCannedMessageController::class, 'applicationStatusAjaxList']);
    Route::get('/master-data/ApplicationStatusCannedMessage', [ApplicationStatusCannedMessageController::class, 'index'])->name('ApplicationStatusCannedMessage.index');
    Route::get('/master-data/ApplicationStatusCannedMessage/getList', [ApplicationStatusCannedMessageController::class, 'getList'])->name('ApplicationStatusCannedMessage.getList');
    Route::any('/master-data/ApplicationStatusCannedMessage/store', [ApplicationStatusCannedMessageController::class, 'store'])->name('ApplicationStatusCannedMessage.store');
    Route::post('/master-data/ApplicationStatusCannedMessage/ActiveInactive', [ApplicationStatusCannedMessageController::class, 'ActiveInactive'])->name('ApplicationStatusCannedMessage.ActiveInactive');

    // ----------- Region ------------
    Route::get('/location/region', [RegionController::class, 'index'])->name('region.index');
    Route::get('/location/region/getList', [RegionController::class, 'getList'])->name('region.getList');
    Route::any('/location/region/store', [RegionController::class, 'store'])->name('region.store');
    Route::post('/location/region/ActiveInactive', [RegionController::class, 'ActiveInactive'])->name('region.ActiveInactive');

    // ----------- Provinces ------------
    Route::post('regionAjaxList', [ProvincesController::class, 'regionAjaxList']);
    Route::get('/location/provinces', [ProvincesController::class, 'index'])->name('provinces.index');
    Route::get('/location/provinces/getList', [ProvincesController::class, 'getList'])->name('provinces.getList');
    Route::any('/location/provinces/store', [ProvincesController::class, 'store'])->name('provinces.store');
    Route::post('/location/provinces/ActiveInactive', [ProvincesController::class, 'ActiveInactive'])->name('provinces.ActiveInactive');

    // ----------- municipality ------------
    Route::any('provincesAjaxList', [AdminMunicipalityController::class, 'provincesAjaxList']);
    Route::post('provinceRegionsAjaxList', [AdminMunicipalityController::class, 'provinceRegionsAjaxList']);
    Route::get('/location/municipality', [AdminMunicipalityController::class, 'index'])->name('municipality.index');
    Route::get('/location/municipality/getList', [AdminMunicipalityController::class, 'getList'])->name('municipality.getList');
    Route::any('/location/municipality/store', [AdminMunicipalityController::class, 'store'])->name('municipality.store');
    Route::post('/location/municipality/ActiveInactive', [AdminMunicipalityController::class, 'ActiveInactive'])->name('municipality.ActiveInactive');

    // -----------Barangay ------------
    Route::any('getBarngayMunProvRegionAjaxList', [AdminBarangayController::class, 'getBarngayMunProvRegionAjaxList']);
    Route::get('/location/barangay', [AdminBarangayController::class, 'index'])->name('barangay.index');
    Route::get('/location/barangay/getList', [AdminBarangayController::class, 'getList'])->name('barangay.getList');
    Route::any('/location/barangay/store', [AdminBarangayController::class, 'store'])->name('barangay.store');
    Route::post('/location/barangay/ActiveInactive', [AdminBarangayController::class, 'ActiveInactive'])->name('barangay.ActiveInactive');

    // -----------CustomerProfile ------------
    Route::get('/user/CustomerProfile', [CustomerProfileController::class, 'index'])->name('CustomerProfile.index');
    Route::get('/user/CustomerProfile/getList', [CustomerProfileController::class, 'getList'])->name('CustomerProfile.getList');
    Route::any('/user/CustomerProfile/store', [CustomerProfileController::class, 'store'])->name('CustomerProfile.store');
    Route::post('/user/CustomerProfile/ActiveInactive', [CustomerProfileController::class, 'ActiveInactive'])->name('CustomerProfile.ActiveInactive');
    Route::delete('/user/customers/{id}', [CustomerProfileController::class, 'destroy'])->name('CustomerProfile.destroy');
    Route::get('/user/check-business-Registration-records/{id}', [CustomerProfileController::class, 'getCheckRecordBusinessRegNum'])->name('CustomerProfile.check-business-Registration-records');
    // -----------Department ------------
    Route::any('getUserAjaxList', [DepartmentController::class, 'getUserAjaxList']);
    Route::get('/security/department', [DepartmentController::class, 'index'])->name('department.index');
    Route::get('/security/department/getList', [DepartmentController::class, 'getList'])->name('department.getList');
    Route::any('/security/department/store', [DepartmentController::class, 'store'])->name('department.store');
    Route::post('/security/department/ActiveInactive', [DepartmentController::class, 'ActiveInactive'])->name('department.ActiveInactive');

    // -----------Documents ------------
    Route::get('/security/documents', [DocumentsController::class, 'index'])->name('documents.index');
    Route::put('/security/documents-update/{id}', [DocumentsController::class, 'document_update'])->name('documents.document_update');

    // ----------- Menu Group ------------
    Route::get('/setting/MenuGroup', [MenuGroupController::class, 'index'])->name('MenuGroup.index');
    Route::get('/setting/MenuGroup/getList', [MenuGroupController::class, 'getList'])->name('MenuGroup.getList');
    Route::any('/setting/MenuGroup/store', [MenuGroupController::class, 'store'])->name('MenuGroup.store');

    // ----------- Menu Module  ------------
    Route::any('getmenuGroupAjaxList', [MenuModuleController::class, 'getmenuGroupAjaxList']);
    Route::get('/setting/MenuModule', [MenuModuleController::class, 'index'])->name('MenuModule.index');
    Route::get('/setting/MenuModule/getList', [MenuModuleController::class, 'getList'])->name('MenuModule.getList');
    Route::any('/setting/MenuModule/store', [MenuModuleController::class, 'store'])->name('MenuModule.store');

    // ----------- Cron-job  ------------
    Route::any('getmenuGroupAjaxList', [CronJobController::class, 'getmenuGroupAjaxList']);
    Route::get('/setting/cron-job', [CronJobController::class, 'index'])->name('cron-job.index');
    Route::get('/setting/cron-job/getList', [CronJobController::class, 'getList'])->name('cron-job.getList');
    Route::any('/setting/cron-job/store', [CronJobController::class, 'store'])->name('cron-job.store');
    Route::post('setting/cron-job/getScheduleVal', [CronJobController::class, 'getScheduleVal']);
    Route::any('allCronDepartmentAjaxList', [CronJobController::class, 'allCronDepartmentAjaxList']);
    Route::post('/cron-job/quickRunCron', [CronJobController::class, 'quickRunCron']);

    // -----------report- Income  ------------
    Route::any('getFeesAjaxList', [IncomeController::class, 'getFeesAjaxList']);
    Route::get('/report/Income', [IncomeController::class, 'index'])->name('Income.index');
    Route::get('/report/Income/getList', [IncomeController::class, 'getList'])->name('Income.getList');
    Route::get('/income/export', [IncomeController::class, 'exportAll'])->name('Income.exportAll');
    

    // -----------Evaluator KPI  ------------
    Route::get('/report/Evaluator-KPI', [EvaluatorKpiController::class, 'index'])->name('EvaluatorKpi.index');
    Route::get('/report/Evaluator-KPI/getList', [EvaluatorKpiController::class, 'getEvaluatorKpiList'])->name('EvaluatorKpi.getList');
    Route::get('/Evaluator-KPI/export', [EvaluatorKpiController::class, 'exportAll'])->name('EvaluatorKpi.exportAll');
    Route::get('/Evaluator-KPI/view/export', [EvaluatorKpiController::class, 'viewexportAll'])->name('EvaluatorKpi.viewexportAll');
    Route::get('/evaluator/business-list/{id}', [EvaluatorKpiController::class, 'getEvaluatorBusinessList'])->name('EvaluatorKpi.getEvaluatorBusinessList');
    // -----------report- Daily  ------------
    Route::get('/report/daily', [DailyReportController::class, 'index'])->name('dailyreport.index');
    Route::get('/report/daily/getList', [DailyReportController::class, 'getList'])->name('dailyreport.getList');
    Route::get('/daily/export', [DailyReportController::class, 'exportAll'])->name('dailyreport.exportAll');

    // -----------report-Archived-applications  ------------
    Route::get('/report/archived-applications', [ArchivedApplicationsReportController::class, 'index'])->name('archivedApplicationsReport.index');
    Route::get('/report/archived-applications/getList', [ArchivedApplicationsReportController::class, 'getList'])->name('archivedApplicationsReport.getList');
    Route::get('/archived-applications/export', [ArchivedApplicationsReportController::class, 'exportAll'])->name('archivedApplicationsReport.exportAll');
    Route::post('/archived-applications/ActiveArchived', [ArchivedApplicationsReportController::class, 'ActiveArchivedApplications'])
    ->name('archived-applications.ActiveArchived');
    // ----------- Evaluator ------------
    Route::post('userAjaxList', [EvaluatorController::class, 'userAjaxList']);
    Route::get('/user/evaluator', [EvaluatorController::class, 'index'])->name('evaluator.index');
    Route::get('/user/evaluator/getList', [EvaluatorController::class, 'getList'])->name('evaluator.getList');
    Route::get('/user/evaluator/create', [EvaluatorController::class, 'create'])->name('evaluator.create');
    Route::any('/user/evaluator/store', [EvaluatorController::class, 'store'])->name('evaluator.store');
    Route::get('/user/evaluator/edit/{id}', [EvaluatorController::class, 'edit'])->name('evaluator.edit');
    Route::post('/user/evaluator/update/{id}', [EvaluatorController::class, 'update'])->name('evaluator_update');
    Route::get('/user/evaluator/destroy/{id}', [EvaluatorController::class, 'destroy'])->name('evaluator_destroy');
    Route::post('/user/evaluator/ActiveInactive', [EvaluatorController::class, 'ActiveInactive'])->name('evaluator.ActiveInactive');

    // -----------audit-trail ------------
    Route::get('/user/audit-trail', [AuditTrailController::class, 'index'])->name('audittrail.index');
    Route::get('/user/audit-trail/getList', [AuditTrailController::class, 'getList'])->name('audittrail.getList');
    Route::any('/user/audit-trail/store', [AuditTrailController::class, 'store'])->name('audittrail.store');
    Route::post('/user/audit-trail/ActiveInactive', [AuditTrailController::class, 'ActiveInactive'])->name('audittrail.ActiveInactive');
    Route::delete('/user/audit-trail/{id}', [AuditTrailController::class, 'destroy'])->name('audittrail.destroy');
    Route::get('/payment/update-status', [PaymentStatusController::class, 'index'])->name('updatepaymet.index');
    Route::post('/payment/update-status/getList', [PaymentStatusController::class, 'getList'])->name('updatepaymet.getList');
    Route::post('/payment/sync-status', [PaymentStatusController::class, 'updatePaymentStatus'])->name('updatepaymet.updateStatus');
    
    // -----------report-Archived-applications  ------------
    Route::get('/report/returned-applications', [ReturnedApplicationsReportController::class, 'index'])->name('returnedApplicationsReport.index');
    Route::post('/report/returned-applications/getList', [ReturnedApplicationsReportController::class, 'getList'])->name('returnedApplicationsReport.getList');
    Route::get('/returned-applications/export', [ReturnedApplicationsReportController::class, 'exportAll'])->name('returnedApplicationsReport.exportAll');

    // -----------refund payment  ------------
    Route::get('/payment/refund', [PaymentStatusController::class, 'refundIndex'])->name('refund.index');
    Route::post('/payment/refund/getList', [PaymentStatusController::class, 'refundGetList'])->name('refund.getList');
    Route::post('/payment/refundAmount', [PaymentStatusController::class, 'refundAmount'])->name('refund.refundAmount');
    Route::post('/payment/getFeeDetails', [PaymentStatusController::class, 'getFeeDetails'])->name('refund.getFeeDetails');

});
