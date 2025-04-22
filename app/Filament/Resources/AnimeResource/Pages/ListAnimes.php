<?php

namespace Liamtseva\Cinema\Filament\Resources\AnimeResource\Pages;

use Liamtseva\Cinema\Filament\Resources\AnimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnimes extends ListRecords
{
    protected static string $resource = AnimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
