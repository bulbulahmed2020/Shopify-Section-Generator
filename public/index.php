<?php

define('LARAVEL_START', microtime(true));

if (file_exists($__composer_autoload_path = __DIR__.'/../vendor/autoload.php')) {
    require $__composer_autoload_path;
} else {
    fwrite(STDERR, 'Composer dependencies not found. Run: composer install'.PHP_EOL);
    http_response_code(500);
    echo 'Composer dependencies not found. Please run: composer install';
    exit(1);
}

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
