<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get an anime from the database
$anime = AnimeSite\Models\Anime::first();

if (!$anime) {
    echo "No anime found in the database.\n";
    exit;
}

echo "Testing API endpoint for anime: {$anime->name} (Slug: {$anime->slug})\n";

// Create a request to the API endpoint
$request = Illuminate\Http\Request::create("/api/v1/animes/{$anime->slug}", 'GET');

// Handle the request through the kernel
$response = $kernel->handle($request);

// Output the response
echo "Response status: " . $response->getStatusCode() . "\n";
echo "Response content: " . $response->getContent() . "\n";

// Now test with ID to see if that works
echo "\nTesting API endpoint with anime ID instead of slug...\n";
$request = Illuminate\Http\Request::create("/api/v1/animes/{$anime->id}", 'GET');

// Handle the request through the kernel
$response = $kernel->handle($request);

// Output the response
echo "Response status: " . $response->getStatusCode() . "\n";
echo "Response content: " . $response->getContent() . "\n";
