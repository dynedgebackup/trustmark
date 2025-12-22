<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use DB;

class DocumentsController extends Controller
{
    
    public function index(Request $request)
    {
        $document = DB::table('setting_documents')->first();
        return view('documents.index',compact('document'));
    }
    public function document_update(Request $request, $id)
{
    $request->validate([
        'path_url' => 'required|file|mimes:doc,docx,pdf|max:10240',
    ]);

    try {
        $document = DB::table('setting_documents')->where('id', $id)->first();
        $updateData = [];

        if ($request->hasFile('path_url')) {
            $file = $request->file('path_url');
            $filename = $file->getClientOriginalName();

            if (!Storage::disk('public')->exists('setting-documents')) {
                Storage::disk('public')->makeDirectory('setting-documents', 0755, true);
            }

            $file->storeAs('setting-documents', $filename, 'public');
            $updateData['path_url'] = 'setting-documents/' . $filename;

            if (
                $document && $document->path_url &&
                Storage::disk('public')->exists($document->path_url)
            ) {
                Storage::disk('public')->delete($document->path_url);
            }
        }

        DB::table('setting_documents')->where('id', $id)->update($updateData);

        return redirect()->route('documents.index')->with('success', 'Updated successfully!');
    } catch (\Exception $e) {
        \Log::error('Update Document Error: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }
}


    
}
