<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Get a person with animes
$person = AnimeSite\Models\Person::first();

if (!$person) {
    echo "No person found in the database.\n";
    exit;
}

echo "Testing API endpoint for person: {$person->name} (Slug: {$person->slug})\n";

// Use curl to make the request
$url = "http://localhost:8000/api/v1/people/{$person->slug}/animes";
echo "URL: $url\n";

// Execute the curl request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response status: $httpCode\n";
if ($response) {
    echo "Response content: $response\n";
} else {
    echo "No response received. Make sure your server is running.\n";
}
