<?php

namespace AnimeSite\Console\Commands;

use Illuminate\Console\Command;
use AnimeSite\Models\Person;
use AnimeSite\Http\Controllers\Api\V1\PersonController;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class TestPersonAnimesRoute extends Command
{
    protected $signature = 'test:person-animes';
    protected $description = 'Test the person animes route';

    public function handle()
    {
        // Get a person from the database
        $person = Person::first();
        
        if (!$person) {
            $this->error('No person found in the database.');
            return 1;
        }
        
        $this->info("Testing with person: {$person->name} (ID: {$person->id}, Slug: {$person->slug})");
        
        // Create a relationship with an anime if none exists
        if ($person->animes()->count() === 0) {
            $anime = \AnimeSite\Models\Anime::first();
            
            if ($anime) {
                $this->info("Creating relationship with anime: {$anime->name}");
                
                $person->animes()->attach($anime->id, [
                    'character_name' => 'Test Character',
                ]);
            }
        }
        
        // Get the animes for this person
        $animes = $person->animes()->get();
        
        $this->info("Number of animes for this person: {$animes->count()}");
        
        foreach ($animes as $anime) {
            $this->info("- {$anime->name} (Character: {$anime->pivot->character_name})");
        }
        
        // Test the controller method
        $this->info("\nTesting controller method...");
        
        $controller = new PersonController();
        $result = $controller->animes($person);
        
        $this->info("Response status: {$result->status()}");
        $this->info("Response content: {$result->content()}");
        
        return 0;
    }
}
