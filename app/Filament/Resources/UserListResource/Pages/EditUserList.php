<?php

namespace AnimeSite\Filament\Resources\UserListResource\Pages;

use AnimeSite\Filament\Resources\UserListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserList extends EditRecord
{
    protected static string $resource = UserListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
