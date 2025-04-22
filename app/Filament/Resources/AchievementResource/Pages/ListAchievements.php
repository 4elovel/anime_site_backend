<?php

namespace Liamtseva\Cinema\Filament\Resources\AchievementResource\Pages;

use Liamtseva\Cinema\Filament\Resources\AchievementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAchievements extends ListRecords
{
    protected static string $resource = AchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
