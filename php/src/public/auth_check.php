<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$request = Request::capture();
$response = $kernel->handle($request);

if (!Auth::check()) {
    http_response_code(401);
    exit;
}

http_response_code(200);
exit;
