<?php

namespace App\Listeners;

use App\Models\UploadedFileHistory;
use App\Events\FileProcessStatusUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateFileProcessStatus implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(FileProcessStatusUpdated $event): void
    {
        // Update the file's status in the database
        $event->file->update(['status' => $event->status]);
    }
}
