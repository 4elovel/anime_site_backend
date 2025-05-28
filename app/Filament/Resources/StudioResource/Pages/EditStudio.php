<?php

namespace AnimeSite\Filament\Resources\StudioResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use AnimeSite\Filament\Resources\StudioResource;

class EditStudio extends EditRecord
{
    protected static string $resource = StudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
