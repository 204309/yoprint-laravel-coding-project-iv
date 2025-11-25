<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void
    {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void
    {
        // Handle PostTooLargeException with better error messages
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request)
        {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'File is too large. Please increase PHP upload limits.',
                    'error' => 'The POST data exceeds the maximum allowed size. Please modify your php.ini file:',
                    'instructions' => [
                        '1. Find your php.ini file (run: php --ini)',
                        '2. Set upload_max_filesize = 1024M',
                        '3. Set post_max_size = 1024M (must be >= upload_max_filesize)',
                        '4. Restart your PHP server',
                    ],
                    'current_limits' => [
                        'upload_max_filesize' => ini_get('upload_max_filesize'),
                        'post_max_size' => ini_get('post_max_size'),
                    ]
                ], 413);
            }

            return redirect()->back()->withErrors([
                'file' => 'File is too large. Please increase PHP upload limits in php.ini (upload_max_filesize and post_max_size).'
            ]);
        });
    })->create();
