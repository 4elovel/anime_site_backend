<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Create a request to the studios endpoint
$request = Illuminate\Http\Request::create('/api/v1/studios', 'GET');

// Get the controller instance
$controller = new AnimeSite\Http\Controllers\Api\V1\StudioController();

// Call the index method
$action = app(AnimeSite\Actions\Studios\GetAllStudios::class);
$result = $controller->index($request, $action);

// Output the result
echo "Response status: " . $result->status() . "\n";
echo "Response content: " . $result->content() . "\n";

// Count studios in the database
$studioCount = AnimeSite\Models\Studio::count();
echo "Number of studios in database: " . $studioCount . "\n";
