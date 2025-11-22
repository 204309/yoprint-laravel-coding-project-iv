<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadedFileHistoryController;


Route::get('/', [UploadedFileHistoryController::class, 'index']);
Route::post('/', [UploadedFileHistoryController::class, 'store']);

