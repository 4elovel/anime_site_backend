<?php

namespace Liamtseva\Cinema\Filament\Resources\RatingResource\Pages;

use Liamtseva\Cinema\Filament\Resources\RatingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRating extends EditRecord
{
    protected static string $resource = RatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
