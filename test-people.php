<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Create a request to the people endpoint
$request = Illuminate\Http\Request::create('/api/v1/people', 'GET');

// Get the controller instance
$controller = new AnimeSite\Http\Controllers\Api\V1\PersonController();

// Call the index method
$action = app(AnimeSite\Actions\People\GetAllPeople::class);
$result = $controller->index($request, $action);

// Output the result
echo "Response status: " . $result->status() . "\n";
echo "Response content: " . $result->content() . "\n";

// Count people in the database
$peopleCount = AnimeSite\Models\Person::count();
echo "Number of people in database: " . $peopleCount . "\n";
