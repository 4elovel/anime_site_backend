<?php

namespace Liamtseva\Cinema\Filament\Resources\NotificationHistoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liamtseva\Cinema\Filament\Resources\NotificationHistoryResource;

class EditNotificationHistory extends EditRecord
{
    protected static string $resource = NotificationHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}