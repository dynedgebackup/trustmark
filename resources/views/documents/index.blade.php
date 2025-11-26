@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">Manage Documents</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="/#"><span>Documents</span></a></li>
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
                <form action="{{ route('documents.document_update', $document->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Internal Redress Mechanism</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col-md-10">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Browse Documents<span
                                                        class="required-field">*</span></label>
                                                        <input class="form-control custom-input" 
                                                        type="file"
                                                        id="path_url" 
                                                        name="path_url"
                                                        accept=".doc,.docx,.pdf"
                                                        title="Please upload .doc, .docx, or .pdf. Max size 10 MB" 
                                                        required>
                                            </div>
                                        </div>
                                        <div class="col-md-2" style="padding-top: 30px;">
                                            @if($document->path_url)
                                            <a href="{{ route('internal.redress.download') }}?v={{ time() }}"
                                            class="btn btn-success mb-2"
                                            style="font-size: 13px;">
                                                Download
                                            </a>
                                            @endif
                                            <button type="submit" class="btn btn-primary" style="margin-top: -8px;font-size: 13px;">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection