<?php

namespace Liamtseva\Cinema\Filament\Resources\SelectionResource\Pages;

use Liamtseva\Cinema\Filament\Resources\SelectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSelection extends EditRecord
{
    protected static string $resource = SelectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
