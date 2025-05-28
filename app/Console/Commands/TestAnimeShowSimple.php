<?php

namespace AnimeSite\Console\Commands;

use Illuminate\Console\Command;
use AnimeSite\Models\Anime;
use AnimeSite\Actions\Animes\ShowAnime;

class TestAnimeShowSimple extends Command
{
    protected $signature = 'test:anime-show-simple';
    protected $description = 'Test the anime show route with a simpler approach';

    public function handle()
    {
        // Get an anime from the database
        $anime = Anime::first();
        
        if (!$anime) {
            $this->error('No anime found in the database.');
            return 1;
        }
        
        $this->info("Testing with anime: {$anime->name} (ID: {$anime->id}, Slug: {$anime->slug})");
        
        // Test the action directly
        $this->info("\nTesting action directly...");
        
        $action = new ShowAnime();
        $result = $action($anime);
        
        $this->info("Anime ID: {$result->id}");
        $this->info("Anime Name: {$result->name}");
        $this->info("Anime Slug: {$result->slug}");
        
        // Test route binding with slug
        $this->info("\nTesting route binding...");
        $this->info("Route key name: {$anime->getRouteKeyName()}");
        $this->info("Route key: {$anime->getRouteKey()}");
        
        return 0;
    }
}
