<?php

namespace AnimeSite\Filament\Resources\AchievementUserResource\Pages;

use AnimeSite\Filament\Resources\AchievementUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAchievementUsers extends ManageRecords
{
    protected static string $resource = AchievementUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
