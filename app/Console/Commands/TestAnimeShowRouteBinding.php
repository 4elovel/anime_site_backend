<?php

namespace AnimeSite\Console\Commands;

use Illuminate\Console\Command;
use AnimeSite\Models\Anime;
use Illuminate\Support\Facades\Route;

class TestAnimeShowRouteBinding extends Command
{
    protected $signature = 'test:anime-show-route-binding';
    protected $description = 'Test the anime show route with the router';

    public function handle()
    {
        // Get an anime from the database
        $anime = Anime::first();
        
        if (!$anime) {
            $this->error('No anime found in the database.');
            return 1;
        }
        
        $this->info("Testing with anime: {$anime->name} (ID: {$anime->id}, Slug: {$anime->slug})");
        
        // Test route binding with slug
        $this->info("\nTesting route binding...");
        
        // Get the route
        $routes = Route::getRoutes();
        $route = null;
        
        foreach ($routes as $r) {
            if ($r->uri() === 'api/v1/animes/{anime}') {
                $route = $r;
                break;
            }
        }
        
        if (!$route) {
            $this->error('Route not found.');
            return 1;
        }
        
        $this->info("Route found: {$route->uri()}");
        $this->info("Route action: " . json_encode($route->getAction()));
        
        // Create a request with the slug
        $request = \Illuminate\Http\Request::create("/api/v1/animes/{$anime->slug}", 'GET');
        
        // Test if the route matches the request
        $this->info("Route matches request: " . ($route->matches($request) ? 'Yes' : 'No'));
        
        return 0;
    }
}
