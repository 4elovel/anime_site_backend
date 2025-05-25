<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Find a person with animes
$person = AnimeSite\Models\Person::whereHas('animes')->first();

if (!$person) {
    echo "No person with animes found in the database.\n";
    exit;
}

echo "Testing with person: {$person->name} (ID: {$person->id}, Slug: {$person->slug})\n";

// Create a request to the person animes endpoint using the slug
$request = Illuminate\Http\Request::create("/api/v1/people/{$person->slug}/animes", 'GET');

try {
    // Get the controller instance
    $controller = new AnimeSite\Http\Controllers\Api\V1\PersonController();

    // Create a route instance to bind the parameter
    $route = new Illuminate\Routing\Route(['GET'], "api/v1/people/{person}/animes", ['person' => $person->slug]);
    $route->bind($request);
    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    // Call the animes method
    $result = $controller->animes($person);

    // Output the result
    echo "Response status: " . $result->status() . "\n";
    echo "Response content: " . $result->content() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Count animes for this person
$animeCount = $person->animes()->count();
echo "\nNumber of animes for this person: " . $animeCount . "\n";
