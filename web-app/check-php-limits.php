<?php
/**
 * Quick script to check PHP upload limits
 * Run this from command line: php check-php-limits.php
 */

echo "=== PHP Upload Limits Check ===\n\n";

$settings = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit'),
];

foreach ($settings as $key => $value) {
    echo sprintf("%-25s: %s\n", $key, $value);
}

echo "\n=== Conversion to Bytes ===\n";
function convertToBytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    $val = (int) $val;
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

echo sprintf(
    "%-25s: %s bytes (%.2f MB)\n",
    'post_max_size',
    number_format(convertToBytes($settings['post_max_size'])),
    convertToBytes($settings['post_max_size']) / 1024 / 1024
);

echo sprintf(
    "%-25s: %s bytes (%.2f MB)\n",
    'upload_max_filesize',
    number_format(convertToBytes($settings['upload_max_filesize'])),
    convertToBytes($settings['upload_max_filesize']) / 1024 / 1024
);

echo "\n=== PHP Configuration File ===\n";
$iniPath = php_ini_loaded_file();
echo "Loaded php.ini: " . ($iniPath ?: 'None found') . "\n";

echo "\n=== Recommendations ===\n";
$postMaxBytes = convertToBytes($settings['post_max_size']);
if ($postMaxBytes < 50 * 1024 * 1024) { // Less than 50MB
    echo "⚠️  WARNING: post_max_size is too small for large file uploads!\n";
    echo "   Your file (38MB) exceeds the current limit of " . number_format($postMaxBytes) . " bytes.\n";
    echo "\n   To fix:\n";
    echo "   1. Open: D:\\Programming\\xampp\\php\\php.ini\n";
    echo "   2. Find: post_max_size\n";
    echo "   3. Change to: post_max_size = 1024M\n";
    echo "   4. Also set: upload_max_filesize = 1024M\n";
    echo "   5. Save the file\n";
    echo "   6. RESTART php artisan serve (stop with Ctrl+C, then start again)\n";
} else {
    echo "✓ PHP limits look good!\n";
    echo "   If you're still getting errors, make sure you restarted 'php artisan serve' after changing php.ini\n";
}

