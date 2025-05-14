<?php

namespace AnimeSite\Filament\Resources\TariffResource\Pages;

use AnimeSite\Filament\Resources\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariff extends EditRecord
{
    protected static string $resource = TariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
