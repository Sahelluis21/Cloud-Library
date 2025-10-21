<?php

require __DIR__ . '/vendor/autoload.php';

$files = [
    __DIR__ . '/routes/web.php',
    __DIR__ . '/app/Http/Controllers/AuthController.php',
    __DIR__ . '/app/Http/Controllers/HomeController.php',
];

foreach ($files as $file) {
    echo "Checking $file\n";
    include $file;
}
