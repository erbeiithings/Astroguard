<?php

// 1. Bikin folder sementara di memori Vercel (/tmp) biar Laravel bisa nulis file
$tmpStorage = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap/cache';

@mkdir($tmpStorage . '/logs', 0777, true);
@mkdir($tmpStorage . '/framework/views', 0777, true);
@mkdir($tmpStorage . '/framework/cache', 0777, true);
@mkdir($tmpStorage . '/framework/sessions', 0777, true);
@mkdir($tmpBootstrap, 0777, true);

// 2. Panggil file inti Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Paksa Laravel pake folder /tmp yang barusan kita bikin
$app->useStoragePath($tmpStorage);
$app->useBootstrapPath('/tmp/bootstrap');

// 4. Jalanin aplikasinya
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);