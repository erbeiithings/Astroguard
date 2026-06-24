<?php

// 1. bikin folder sementara di memori Vercel (/tmp) biar laravel bisa nulis file
$tmpStorage = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap/cache';

@mkdir($tmpStorage . '/logs', 0777, true);
@mkdir($tmpStorage . '/framework/views', 0777, true);
@mkdir($tmpStorage . '/framework/cache', 0777, true);
@mkdir($tmpStorage . '/framework/sessions', 0777, true);
@mkdir($tmpBootstrap, 0777, true);

// 2. panggil file inti laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. paksa laravel pake folder /tmp
$app->useStoragePath($tmpStorage);
$app->useBootstrapPath('/tmp/bootstrap');

// 4. jalanin programnya
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);