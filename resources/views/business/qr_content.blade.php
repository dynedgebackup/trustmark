<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRUSTMARK</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
</head>

<body>
    <div class="d-flex min-vh-100 align-items-center p-7">
        <div class="container shadow-lg pt-3 pb-4 px-4" style="border-radius: 15px;border-width: 1px;">
            <div class="row">
                <div class="col">
                    <p style="text-align: center;font-size: 24px;font-weight: bold;color: rgb(0,74,172);">TRUSTMARK
                        HOLDER INFORMATION</p>
                </div>
            </div>
            <div class="row">
                <div class="col ps-4 pe-4 pt-0 pb-3 my-0" style="text-align: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                        viewBox="0 0 16 16" class="bi bi-check-circle-fill"
                        style="font-size: 61px;color: var(--bs-teal);">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @if (isset($business->status) && strtoupper($business->status) === 'APPROVED')
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>BUSINESS NAME</td>
                                        <td>{{ $business->business_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SECURITY NO.</td>
                                        <td>{{ $business->trustmark_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>STATUS</td>
                                        <td>{{ $business->status ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>DATE ISSUED</td>
                                        <td>{{ $business->date_issued ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>EXPIRATION</td>
                                        <td>{{ $business->expired_date ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>URL PLATFORM</td>
                                        <td>
                                            @if (!empty($business->url_platform) && is_array($business->url_platform))
                                                @foreach ($business->url_platform as $url)
                                                    <a href="{{ $url }}" target="_blank" class="custom-label">{{ $url }}</a><br>
                                                @endforeach
                                            @else
                                                <span class="custom-label">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="mb-0" style="text-align: center;">NO DATA FOUND</p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col py-5">
                    <p class="mt-0 mb-1 pb-0" style="text-align: center;font-style: italic;">This
                        merchant has been officially recognized by the DTI for <br>maintaining safe, secure and
                        trustworthy e-commerce practices.</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>