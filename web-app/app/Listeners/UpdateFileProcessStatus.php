<?php

namespace App\Listeners;

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
        // The event implements ShouldBroadcast, so it auto-broadcasts via Reverb
        info('Broadcasting status update for file ID ' . $event->uploadedFilePath->id . ' to status: ' . $event->status);
    }
}
