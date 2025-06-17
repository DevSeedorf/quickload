<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../public/index.php';

// Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
