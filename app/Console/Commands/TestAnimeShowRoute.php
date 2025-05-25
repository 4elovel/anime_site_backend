<?php

namespace AnimeSite\Console\Commands;

use Illuminate\Console\Command;
use AnimeSite\Models\Anime;
use AnimeSite\Http\Controllers\Api\V1\AnimeController;
use AnimeSite\Actions\Animes\ShowAnime;

class TestAnimeShowRoute extends Command
{
    protected $signature = 'test:anime-show';
    protected $description = 'Test the anime show route';

    public function handle()
    {
        // Get an anime from the database
        $anime = Anime::first();
        
        if (!$anime) {
            $this->error('No anime found in the database.');
            return 1;
        }
        
        $this->info("Testing with anime: {$anime->name} (ID: {$anime->id}, Slug: {$anime->slug})");
        
        // Test the controller method
        $this->info("\nTesting controller method...");
        
        $controller = new AnimeController();
        $action = new ShowAnime();
        $result = $controller->show($anime, $action);
        
        $this->info("Response status: {$result->status()}");
        $this->info("Response content: {$result->content()}");
        
        // Test route binding with slug
        $this->info("\nTesting route binding...");
        $this->info("Route key name: {$anime->getRouteKeyName()}");
        $this->info("Route key: {$anime->getRouteKey()}");
        
        return 0;
    }
}
