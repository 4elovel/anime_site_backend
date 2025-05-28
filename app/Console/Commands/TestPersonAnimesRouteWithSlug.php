<?php

namespace AnimeSite\Console\Commands;

use Illuminate\Console\Command;
use AnimeSite\Models\Person;
use Illuminate\Support\Facades\Route;

class TestPersonAnimesRouteWithSlug extends Command
{
    protected $signature = 'test:person-animes-slug';
    protected $description = 'Test the person animes route with slug';

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
        
        // Test route binding with slug
        $this->info("\nTesting route binding with slug...");
        
        // Get the route
        $routes = Route::getRoutes();
        $route = null;
        
        foreach ($routes as $r) {
            if ($r->uri() === 'api/v1/people/{person}/animes') {
                $route = $r;
                break;
            }
        }
        
        if (!$route) {
            $this->error('Route not found.');
            return 1;
        }
        
        $this->info("Route found: {$route->uri()}");
        
        // Create a request with the slug
        $request = Request::create("/api/v1/people/{$person->slug}/animes", 'GET');
        
        // Get the route parameters
        $parameters = $route->bind($request)->parameters();
        
        $this->info("Route parameters: " . json_encode($parameters));
        
        // Check if the person parameter is resolved correctly
        if (isset($parameters['person']) && $parameters['person'] instanceof Person) {
            $this->info("Person resolved correctly: {$parameters['person']->name}");
        } else {
            $this->error("Person not resolved correctly.");
        }
        
        return 0;
    }
}
