<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UploadedFileHistoryResource;
use App\Jobs\ProcessUploadedFile;
use App\Models\UploadedFileHistory;
use Illuminate\Http\Request;

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
        ], [
            'file.mimes' => 'The file must be a CSV file.',
        ]);

        // store file and save path + original name
        $uploaded = $request->file('file');

        $file = UploadedFileHistory::create([
            'file_name' => $uploaded->getClientOriginalName(),
            'stored_path' => $uploaded->store('csv_uploads'), // store the file temporarily
            'status' => 'pending',
            'file_size' => $uploaded->getSize(),
        ]);
        // info('FILE STORED');


        // Dispatch the job to process the uploaded file (queued)
        ProcessUploadedFile::dispatch($file);

        // Check if the request expects JSON (API request) or HTML (web request)
        if ($request->expectsJson() || $request->wantsJson()) {
            // API endpoint response - use transformer
            return new UploadedFileHistoryResource($file);
        }

        // Web request - redirect to home page 
        return redirect('/');
    }
}
