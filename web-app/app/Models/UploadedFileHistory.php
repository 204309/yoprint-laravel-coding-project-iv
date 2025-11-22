<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedFileHistory extends Model
{
    protected $fillable = ['file_name', 'stored_path', 'status', 'completed_at'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}


