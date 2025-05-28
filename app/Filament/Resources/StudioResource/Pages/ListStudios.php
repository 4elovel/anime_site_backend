<?php

namespace AnimeSite\Filament\Resources\StudioResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use AnimeSite\Filament\Resources\StudioResource;

class ListStudios extends ListRecords
{
    protected static string $resource = StudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
