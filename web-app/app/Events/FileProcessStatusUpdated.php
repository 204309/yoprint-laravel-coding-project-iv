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
    public function __construct(public UploadedFileHistory $file, public string $status)
    {
        //
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
        return [
            'file_id' => $this->file->id,
            'status' => $this->status,
            'file_name' => $this->file->file_name,
            'uploaded_at' => $this->file->created_at->toDateTimeString(),
        ];
    }
}
