<?php

namespace Liamtseva\Cinema\Filament\Resources\PersonResource\Pages;

use Liamtseva\Cinema\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeople extends ListRecords
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
