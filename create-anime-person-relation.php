<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get a person and an anime from the database
$person = AnimeSite\Models\Person::first();
$anime = AnimeSite\Models\Anime::first();

if (!$person || !$anime) {
    echo "Person or anime not found in the database.\n";
    exit;
}

echo "Person: {$person->name} (ID: {$person->id})\n";
echo "Anime: {$anime->name} (ID: {$anime->id})\n";

// Create a relationship between the person and the anime
try {
    // Check if the relationship already exists
    $exists = $person->animes()->where('anime_id', $anime->id)->exists();
    
    if ($exists) {
        echo "Relationship already exists.\n";
    } else {
        // Create the relationship
        $person->animes()->attach($anime->id, [
            'character_name' => 'Test Character',
        ]);
        
        echo "Relationship created successfully.\n";
    }
    
    // Verify the relationship
    $count = $person->animes()->count();
    echo "Number of animes for this person: {$count}\n";
    
    // Get the animes for this person
    $animes = $person->animes()->get();
    echo "Animes for this person:\n";
    foreach ($animes as $anime) {
        echo "- {$anime->name} (Character: {$anime->pivot->character_name})\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
