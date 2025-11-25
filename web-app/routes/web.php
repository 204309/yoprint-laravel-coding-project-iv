<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadedFileHistoryController;


Route::get('/', [UploadedFileHistoryController::class, 'index']);
Route::post('/', [UploadedFileHistoryController::class, 'store']);

// Diagnostic route to check PHP settings (remove in production)
// Route::get('/php-info', function ()
// {
//     function convertToBytes($val)
//     {
//         $val = trim($val);
//         $last = strtolower($val[strlen($val) - 1] ?? '');
//         $val = (int) $val;
//         switch ($last) {
//             case 'g':
//                 $val *= 1024;
//             case 'm':
//                 $val *= 1024;
//             case 'k':
//                 $val *= 1024;
//         }
//         return $val;
//     }

//     $postMaxSize = ini_get('post_max_size');
//     $uploadMaxSize = ini_get('upload_max_filesize');

//     // Get detailed PHP information
//     $phpIniLoaded = php_ini_loaded_file();
//     $phpIniScanned = php_ini_scanned_files();
//     $phpVersion = phpversion();
//     $phpSapi = php_sapi_name();
//     $phpBinary = defined('PHP_BINARY') ? PHP_BINARY : 'Unknown';

//     // Try to find which PHP executable is being used
//     $whichPhp = '';
//     if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
//         $whichPhp = @exec('where php 2>&1');
//     }

//     return response()->json([
//         'upload_max_filesize' => $uploadMaxSize,
//         'post_max_size' => $postMaxSize,
//         'max_execution_time' => ini_get('max_execution_time'),
//         'max_input_time' => ini_get('max_input_time'),
//         'memory_limit' => ini_get('memory_limit'),
//         'post_max_size_bytes' => convertToBytes($postMaxSize),
//         'upload_max_filesize_bytes' => convertToBytes($uploadMaxSize),
//         'php_ini_loaded_file' => $phpIniLoaded,
//         'php_ini_scanned_files' => $phpIniScanned,
//         'php_version' => $phpVersion,
//         'php_sapi' => $phpSapi,
//         'php_binary' => $phpBinary,
//         'php_path_from_system' => $whichPhp,
//         'warning' => convertToBytes($postMaxSize) < 50 * 1024 * 1024
//             ? '⚠️ post_max_size is too small! The web server is using a DIFFERENT php.ini than CLI. Check php_ini_loaded_file above.'
//             : 'Settings look good.',
//         'instructions' => [
//             '1. Check "php_ini_loaded_file" - this is the php.ini file the web server is using',
//             '2. Open that file in a text editor',
//             '3. Find and change: post_max_size = 1024M and upload_max_filesize = 1024M',
//             '4. Save the file',
//             '5. Restart php artisan serve',
//             '6. Check this page again to verify'
//         ]
//     ]);
// });

