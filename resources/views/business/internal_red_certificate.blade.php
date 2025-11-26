<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Trustmark</title>
    <link rel="stylesheet" href="{{ public_path('assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400&display=swap" rel="stylesheet">
</head>


<style>
    
    @font-face {
        font-family: 'Montserrat';
        src: url('{{ public_path('fonts/montserrat/Montserrat-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
        color:#000;
    }

    @font-face {
        font-family: 'Baskerville';
        src: url('{{ public_path('fonts/baskerville/Baskerville-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
        color:#000;
    }

    body {
        
        color:#000;
    }

    .trustmark-title {
        font-family: 'Baskerville', serif;
    }

    .text-justify {
        text-align: justify;
        color:#000;
    }

    @page {
        size: A4;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 40px;
        /* Outer margin for A4 */
    }

    .certificate-container {
    width: calc(100% - 60px); 
    height: calc(100% - 60px);
    padding: 30px;
    margin: 0 auto;
    box-sizing: border-box;
}
</style>

<body>
    <div class="certificate-container">
    <table width="100%" cellpadding="0" cellspacing="0">
        
        <tr>
            <td width="100%" align="center" style="font-size: 11px;color:#000; line-height: normal;">
            <b>{{ strtoupper($business->business_name) }}</b><br>INTERNAL REDRESS MECHANISM POLICY
            </td>
        </tr>
    </table>

        <div class="row mt-3">
           
            @php
                    $nameLength = strlen($business->business_name);
                    if ($nameLength <= 30) {
                        $fontSize = 20;
                    }
                    elseif ($nameLength <= 38) {
                        $fontSize = 18;
                    } elseif ($nameLength <= 50) {
                        $fontSize = 16;
                    } elseif ($nameLength <= 70) {
                        $fontSize = 15;
                    } else {
                        $fontSize = 14;
                    }
                @endphp
                <!-- <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                            <div style="font-weight: bold;color:#4a4444; font-size: {{ $fontSize }}px;word-wrap: break-word !important;padding-left:10px;padding-right:10px;text-align:center;">
                                {{ $business->business_name }}
                            </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                
                </table> -->
                
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">As a <b>{{ $type_corporations->name }}</b> operating under the name <b>{{ $business->business_name }}</b>, I/we are committed to fully complying with Republic Act No. 11967, the Internet Transactions Act (ITA) of 2023, along with other applicable laws and regulations. In alignment with the principles of fair, transparent and accountable digital business practices, I/we, the undersigned, hereby commit to establishing and implementing an internal redress mechanism within our organization.</div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                    
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">This mechanism shall be clear, effective and responsive, and shall be designed to promptly address and resolve consumer concerns, complaints and disputes in a manner that upholds the rights of consumers while fostering trust and confidence in our digital platforms. I/we recognize the importance of consumer trust in the growth of e-commerce and affirm our responsibility to act in good faith, with diligence and fairness, in all transactions and engagements with the public.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                    
                </table>
                
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; text-align: justify;">
                        <div class="mb-0" style="text-align:left;"><b>1. How to File a Complaint</b></div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">Customers may file a complaint and submit the Complaint Form (Annex A) through any of the following official channels (as applicable):
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                @php
                    $socialPages = json_decode($business_irm->social_media_page, true);
                    $online_platform = json_decode($business_irm->online_platform, true);
                    $messaging_apps = json_decode($business_irm->messaging_apps, true);
                    $reso_not_limited_to = json_decode($business_irm->reso_not_limited_to, true);
                @endphp
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="15%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">•	Social Media Page: <b>@if(!empty($socialPages))
                                {{ implode(', ', $socialPages) }}
                            @endif </b>
                        <!-- @if(!empty($socialPages))
                            <ul>
                                @foreach($socialPages as $url)
                                    <li>
                                        <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif -->
                        <br>•	Online Platform Chat (e.g., Lazada/Shopee): <b>@if(!empty($online_platform))
                                {{ implode(', ', $online_platform) }}
                            @endif</b>
                            <!-- @if(!empty($online_platform))
                            <ul>
                                @foreach($online_platform as $url)
                                    <li>
                                        <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif -->
                        <br>•	Email: <b>{{$business_irm->irm_busn_email}}</b>
                        <br>•	Messaging Apps (e.g., WhatsApp/Viber): <b>@if(!empty($messaging_apps))
                                {{ implode(', ', $messaging_apps) }}
                            @endif</b>
                        <!-- @if(!empty($messaging_apps))
                            <ul>
                                @foreach($messaging_apps as $url)
                                    <li>
                                        <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif -->
                        </div>
                        </td>
                        <td width="5%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">To facilitate efficient handling, please include your full name, invoice or transaction number, date of transaction, and a detailed description of the issue.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; text-align: justify;">
                        <div class="mb-0" style="text-align:left;"><b>2. Acknowledgment of Complaint</b></div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
               
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">All complaints will be acknowledged within <b>{{ $business_irm->complaint_hour == 1 ? '24-hours' :
   ($business_irm->complaint_hour == 2 ? '48-hours' :
   ($business_irm->complaint_hour == 3 ? '72-hours' : 'others')) }}</b>. An initial response will confirm receipt and outline the next steps in the resolution process.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; text-align: justify;">
                        <div class="mb-0" style="text-align:left;"><b>3. Investigation and Resolution</b></div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">Each complaint will be reviewed thoroughly. We will validate the facts and determine the most appropriate action. Depending on the complexity, a resolution will be provided within <b>{{ $business_irm->reso_hours == 1 ? '24-hours' :
   ($business_irm->reso_hours == 2 ? '3-days' :
   ($business_irm->reso_hours == 3 ? '7-days' : 'others')) }}</b> from acknowledgment.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">Resolutions may include, but are not limited to:
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="15%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">@foreach($reso_not_limited_to as $url)
                            • {{ $url }}<br>
                        @endforeach
                        </div>
                        </td>
                        <td width="5%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; font-family: montserrat; text-align: justify;">
                        <div class="mb-0" style="text-align:left;font-family:'Montserrat', serif !important">4. Customer Feedback and Closure</div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">After providing a resolution, we will confirm with the customer whether the issue has been satisfactorily resolved. All complaints and actions taken are documented for future reference and service improvement.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; font-family: montserrat; text-align: justify;">
                        <div class="mb-0" style="text-align:left;font-family:'Montserrat', serif !important">5. Escalation (if necessary)</div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">In case the customer is not satisfied with our proposed resolution or action taken, he/she may escalate the matter to relevant consumer protection agencies such as:
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="15%"></td>
                        <td width="75%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">•	Department of Trade and Industry (DTI) – E-Commerce Bureau (ECB) via email <a href="ecommerce_complaints@dti.gov.ph" target="_blank">ecommerce_complaints@dti.gov.ph</a> 
                        <br>•	Or via the Consumer Care website <a href="https://podrs.dti.gov.ph/" target="_blank">https://podrs.dti.gov.ph/</a> , Consumer Care Hotline at DTI (1–-384),or send an email to <a href="consumercare@dti.gov.ph" target="_blank">consumercare@dti.gov.ph</a> 
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;">This Internal Redress Mechanism is aligned with the requirements of the ITA and its Implementing Rules and Regulations and is established in accordance with consumer protection principles and other applicable laws and regulations related to e-commerce. It also serves as a demonstration of my responsibility as a digital market participant in promoting trust, accountability, and safety in the online marketplace.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
                
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; text-align: justify;">
                            <div class="mb-0" style="text-align:left;"><br><br><br><b>{{$business_irm->authorized_rep}}</b>
                            <br><b>{{$business_irm->authorized_rep_position}}</b>
                            <br><b>{{$business_irm->busn_name}}</b></div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
            </div>
        </div>
        
    </div>
</body>

</html>