<?php

// Get the person slug from the database
$pdo = new PDO('mysql:host=localhost;dbname=animesite', 'root', '');
$stmt = $pdo->query('SELECT id, slug, name FROM people WHERE id IN (SELECT person_id FROM anime_person) LIMIT 1');
$person = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$person) {
    echo "No person with animes found in the database.\n";
    
    // Get any person
    $stmt = $pdo->query('SELECT id, slug, name FROM people LIMIT 1');
    $person = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$person) {
        echo "No person found in the database.\n";
        exit;
    }
    
    echo "Using person without animes: {$person['name']} (ID: {$person['id']}, Slug: {$person['slug']})\n";
} else {
    echo "Testing with person: {$person['name']} (ID: {$person['id']}, Slug: {$person['slug']})\n";
}

// Use curl to make the request
$url = "http://localhost:8000/api/v1/people/{$person['slug']}/animes";
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
