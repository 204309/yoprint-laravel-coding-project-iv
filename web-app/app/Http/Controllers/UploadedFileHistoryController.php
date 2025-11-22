<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UploadedFileHistoryResource;
use App\Jobs\ProcessUploadedFile;
use App\Models\UploadedFileHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class UploadedFileHistoryController extends Controller
{

    public function index(UploadedFileHistory $uploadedFileHistory)
    {
        $uploadedFiles = UploadedFileHistory::latest()->get();
        return view('home', compact('uploadedFiles'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv'],
        ]);
        // store file and save path + original name
        $uploaded = $request->file('file'); 

        $file = UploadedFileHistory::create([
            'file_name' => $uploaded->getClientOriginalName(),
            'stored_path' => $uploaded->store('csv_uploads'), // store the file temporarily
            'status' => 'pending',
        ]);

        // Dispatch the job to process the uploaded file (queued)
        ProcessUploadedFile::dispatch($file);

        // return redirect()->back()->with('success', 'File uploaded successfully and is being processed.');
        // API endpoint response
        return new UploadedFileHistoryResource($file);

        }   
}
