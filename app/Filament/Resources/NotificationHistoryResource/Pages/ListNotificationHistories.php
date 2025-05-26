<?php

namespace Liamtseva\Cinema\Filament\Resources\NotificationHistoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liamtseva\Cinema\Filament\Resources\NotificationHistoryResource;

class ListNotificationHistories extends ListRecords
{
    protected static string $resource = NotificationHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}