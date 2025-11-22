<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('file-process-status-updates', function () {
    return true;
});
