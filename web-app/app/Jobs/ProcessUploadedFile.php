<?php

namespace App\Jobs;

use App\Models\Product;
use function Laravel\Prompts\info;
use App\Models\UploadedFileHistory;
use Illuminate\Support\Facades\Storage;
use App\Events\FileProcessStatusUpdated;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        // info('Processing uploaded file name: ' . $this->uploadedFilePath->file_name);

        FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'processing');
        $this->uploadedFilePath->update(['status' => 'processing']);

        $file = storage_path('app/private/' . $this->uploadedFilePath->stored_path);
        // info('FILE: ' . $file);

        if (!file_exists($file) || !is_readable($file)) {
            // info('FILE DOES NOT EXIST OR IS NOT READABLE');
            $this->uploadedFilePath->update(['status' => 'failed']);
            FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'failed');
            return;
        }

        try {
            // info('INSIDE TRY BLOCK');
            $product = new Product();

            // Lowercase fillable and uniqueBy for matching
            $uniqueBy = array_map('strtolower', Product::uniqueBy());

            // $fillable = array_map('strtolower', $product->getFillable());
            // info('INSIDE FILLABLE: ' . json_encode($fillable));

            if (($process = fopen($file, 'r')) !== false) {
                // info('FILE IS OPEN');
                
                // Read header
                $rawHeader = fgetcsv($process, 0, ',');
                if (!$rawHeader) {
                    throw new \Exception('Invalid CSV header.');
                }
                // info('RAW HEADER: ' . json_encode($rawHeader));

                // Normalizer: remove BOM/zero-width, convert to utf8, replace non-alnum with underscore, lowercase
                $normalize = function ($h)
                {
                    $h = mb_convert_encoding($h, 'UTF-8', 'auto');
                    $h = preg_replace('/\x{FEFF}|\x{200B}/u', '', $h); // BOM / zero-width
                    $h = trim($h);

                    // replace sequences of non-letter/number/underscore with single underscore
                    $h = preg_replace('/[^\p{L}\p{N}_]+/u', '_', $h);
                    $h = strtolower($h);
                    $h = trim($h, '_');
                    return $h;
                };

                // Build mapping of normalized fillable => actual fillable name
                $fillableMap = [];
                foreach ($product->getFillable() as $orig) {
                    $fillableMap[$normalize($orig)] = $orig;
                }
                // info('FILLABLE MAP: ' . json_encode($fillableMap));
                
                // normalized unique keys
                $uniqueBy = Product::uniqueBy();
                $uniqueByNormalized = array_map($normalize, $uniqueBy);
                // info('UNIQUE BY NORMALIZED: ' . json_encode($uniqueByNormalized));

                // Normalize header
                $header = array_map($normalize, $rawHeader);
                // info('NORMALIZED HEADER: ' . json_encode($header));

                while (($row = fgetcsv($process, 0, ',')) !== false) {
                    // Clean row values to UTF-8 and remove BOM/zero-width
                    $row = array_map(function ($value)
                    {
                        $value = mb_convert_encoding($value, 'UTF-8', 'auto');
                        $value = preg_replace('/\x{FEFF}|\x{200B}/u', '', $value);
                        return trim($value);
                    }, $row);
                    // info('ROW: ' . json_encode($row));

                    // Combine header → row
                    $combined = @array_combine($header, $row);
                    if (!$combined) {
                        continue; // skip malformed
                    }
                    // info('COMBINED: ' . json_encode($combined));

                    // Map normalized header keys back to actual DB column names using fillableMap
                    $data = [];
                    foreach ($combined as $normKey => $val) {
                        if (isset($fillableMap[$normKey])) {
                            $data[$fillableMap[$normKey]] = $val;
                        }
                    }
                    // info('DATA: ' . json_encode($data));

                    // Build uniqueData using original uniqueBy names (mapped from normalized header)
                    $uniqueData = [];
                    foreach ($uniqueBy as $idx => $origUnique) {
                        $norm = $uniqueByNormalized[$idx];
                        if (isset($combined[$norm])) {
                            // If CSV provided the unique column, use it (and map to original name)
                            $uniqueData[$origUnique] = $combined[$norm];
                        } elseif (isset($data[$origUnique])) {
                            $uniqueData[$origUnique] = $data[$origUnique];
                        }
                    }
                    // info('UNIQUE DATA: ' . json_encode($uniqueData));

                    // require all unique keys to be present
                    if (count($uniqueData) !== count($uniqueBy)) {
                        continue;
                    }

                    // Finally upsert
                    Product::updateOrCreate($uniqueData, $data);
                    // info('UPSERTED RECORD');
                }


                fclose($process);
                // info('FILE CLOSED');
            }

            $this->uploadedFilePath->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            // info('UPDATED FINAL STATUS TO COMPLETED');

            // delete after processing
            Storage::delete($this->uploadedFilePath->stored_path);
            // info('DELETED FILE FROM STORAGE');

            FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'completed');
            // info('DISPATCHED COMPLETED');
        } catch (\Throwable $th) {
            // info('THROWN');
            $this->uploadedFilePath->update([
                'status' => 'failed',
            ]);
            FileProcessStatusUpdated::dispatch($this->uploadedFilePath, 'failed');

            // rethrow for job retries to occur
            throw $th;
        }
    }
}
