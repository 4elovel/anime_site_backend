<?php

namespace AnimeSite\Filament\Resources\EpisodeResource\Pages;

use AnimeSite\Filament\Resources\EpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEpisodes extends ListRecords
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
