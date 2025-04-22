<?php

namespace Liamtseva\Cinema\Filament\Resources\EpisodeResource\Pages;

use Liamtseva\Cinema\Filament\Resources\EpisodeResource;
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
