<?php

namespace App\Jobs;

use App\Events\FileProcessStatusUpdated;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\UploadedFileHistory;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use function Laravel\Prompts\info;

class ProcessUploadedFile implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public UploadedFileHistory $uploadedFilePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('Processing uploaded file name: ' . $this->uploadedFilePath->file_name);

        FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'processing');
        

        $this->uploadedFilePath->update(['status' => 'processing']);
        info('BEFORE IF N TRY METHOD');

        $file = storage_path('app/' . $this->uploadedFilePath->stored_path);

        if (!file_exists($file) || !is_readable($file)) {
            $this->uploadedFilePath->update(['status' => 'failed']);
            FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'failed');

            return;
        }

        try {
            $product = new Product();
            $fillable = $product->getFillable();
            $uniqueBy = Product::uniqueBy();

            info('INSIDE: ' . $fillable);

            if (($process = fopen($file, 'r')) !== false) {

                // Read header
                $rawHeader = fgetcsv($process, 0, ',');
                if (!$rawHeader) {
                    throw new \Exception('Invalid CSV header.');
                }

                // UTF-8 clean + normalize header into snake_case
                $header = array_map(function ($h)
                {
                    $h = mb_convert_encoding($h, 'UTF-8', 'auto');
                    return Str::snake(Str::lower(trim($h)));
                }, $rawHeader);

                while (($row = fgetcsv($process, 0, ',')) !== false) {

                    // Clean row values to UTF-8
                    $row = array_map(fn($value) => mb_convert_encoding($value, 'UTF-8', 'auto'), $row);

                    // Combine header → row
                    $data = @array_combine($header, $row);
                    if (!$data)
                        continue; // Skip malformed rows

                    // Only keep fillable DB fields
                    $data = array_intersect_key($data, array_flip($fillable));

                    // If UNIQUE_KEY is missing, skip row
                    $uniqueData = array_intersect_key($data, array_flip($uniqueBy));
                    if (count($uniqueData) !== count($uniqueBy)) {
                        continue;
                    }

                    // UPSERT
                    Product::updateOrCreate(
                        $uniqueData,   
                        $data          
                    );
                }

                fclose($process);
            }

            $this->uploadedFilePath->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'completed');

        } catch (\Throwable $th) {
            //throw $th;
            // mark failed and optionally record $th->getMessage() in a log column
            $this->uploadedFilePath->update([
                'status' => 'failed',
            ]);
            // rethrow for job retries to occur
            throw $th;
        }
    }
}
