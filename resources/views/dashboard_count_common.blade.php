 <style>
    .border-left-primary {
        border-left: .25rem solid #4e73df !important;
        height: 100%;
    }
    .border-left-danger {
        border-left: .25rem solid #e74a3b !important;
        height: 100%;
    }
    .border-left-success {
        height: 100%;
        border-left: .25rem solid #1cc88a !important;
    }
 </style>
 <div class="row" style="margin-bottom: 15px;">
                <div class="col">
                    <div class="card shadow border-left-primary py-2">
                        <div class="card-body">
                            <a href="{{ route('business.business-app') }}" style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-primary fw-bold text-xs mb-1">
                                            <span style="font-family: sans-serif; font-size: 12px;">Business
                                                Application</span>
                                        </div>
                                        <div class="text-dark fw-bold h5 mb-0">
                                            <span>{{ number_format($allApplicationCount) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow border-left-primary py-2">
                        <div class="card-body">
                            <a href="{{ route('business.draft') }}" style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-primary fw-bold text-xs mb-1">
                                            <span style="font-family: sans-serif; font-size: 12px;">Draft</span>
                                        </div>
                                        <div class="text-dark fw-bold h5 mb-0">
                                            <span>{{ number_format($drafts) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow border-left-warning py-2">
                        <div class="card-body">
                            <a href="{{ route('business.under-evaluation') }}"
                                style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-warning fw-bold text-xs mb-1"><span
                                                style="font-family: sans-serif;font-size: 12px;">Under Evaluation</span>
                                        </div>
                                        <div class="text-dark fw-bold h5 mb-0">
                                            <span>{{ number_format($under_evaluations) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow border-left-danger py-2">
                        <div class="card-body">
                            <a href="{{ route('business.list-returned') }}" style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-danger fw-bold text-xs mb-1"
                                            style="font-family: sans-serif;font-size: 12px;"><span>Returned</span></div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto">
                                                <div class="text-dark fw-bold h5 mb-0 me-3">
                                                    <span>{{ number_format($returns) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow border-left-danger py-2">
                        <div class="card-body">
                            <a href="{{ route('business.list-disapproved') }}"
                                style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-danger fw-bold text-xs mb-1"
                                            style="font-family: sans-serif;font-size: 12px;"><span>Disapproved</span></div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto">
                                                <div class="text-dark fw-bold h5 mb-0 me-3">
                                                    <span>{{ number_format($disapproves) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow border-left-success py-2">
                        <div class="card-body">
                            <a href="{{ route('business.list-approved') }}" style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-success fw-bold text-xs mb-1"
                                            style="font-family: sans-serif;font-size: 12px;"><span>Approved</span></div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto">
                                                <div class="text-dark fw-bold h5 mb-0 me-3">
                                                    <span>{{ number_format($approves) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow border-left-success py-2">
                        <div class="card-body">
                            <a href="{{ route('business.list-paid') }}" style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-success fw-bold text-xs mb-1"
                                            style="font-family: sans-serif;font-size: 12px;"><span>Paid</span></div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto">
                                                <div class="text-dark fw-bold h5 mb-0 me-3">
                                                    <span>{{ number_format($paid) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->role == 2)  
                <div class="col">
                    <div class="card shadow border-left-warning py-2">
                        <div class="card-body">
                            <a href="{{ route('business.list_on_hold') }}"
                                style="text-decoration: none; color: inherit;">
                                <div class="row g-0 align-items-center">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-warning fw-bold text-xs mb-1"><span
                                                style="font-family: sans-serif;font-size: 12px;">Under Evaluation (On-hold)</span>
                                        </div>
                                        <div class="text-dark fw-bold h5 mb-0">
                                            <span>{{ number_format($onhold) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>