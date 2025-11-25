<?php

namespace App\Events;

use App\Models\UploadedFileHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileProcessStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public UploadedFileHistory $uploadedFilePath, public string $status)
    {
        $this->uploadedFilePath = $uploadedFilePath;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('file-process-status-updates'),
        ];
    }

    public function broadcastWith(): array
    {

        $statusHtml = view('components.status', ['status' => $this->status])->render();

        return [
            'file_id' => $this->uploadedFilePath->id,
            'file_name' => $this->uploadedFilePath->file_name,
            'status' => $this->status,
            'status_html' => $statusHtml,
        ];
    }
}
