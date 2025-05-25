<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get a person with animes
$person = AnimeSite\Models\Person::whereHas('animes')->first();

if (!$person) {
    echo "No person with animes found in the database.\n";
    exit;
}

echo "Testing API endpoint for person: {$person->name} (Slug: {$person->slug})\n";

// Create a request to the API endpoint
$request = Illuminate\Http\Request::create("/api/v1/people/{$person->slug}/animes", 'GET');

// Handle the request through the kernel
$response = $kernel->handle($request);

// Output the response
echo "Response status: " . $response->getStatusCode() . "\n";
echo "Response content: " . $response->getContent() . "\n";

// Now test with ID to see if that works
echo "\nTesting API endpoint with person ID instead of slug...\n";
$request = Illuminate\Http\Request::create("/api/v1/people/{$person->id}/animes", 'GET');

// Handle the request through the kernel
$response = $kernel->handle($request);

// Output the response
echo "Response status: " . $response->getStatusCode() . "\n";
echo "Response content: " . $response->getContent() . "\n";
