<?php

namespace Liamtseva\Cinema\Filament\Resources\UserListResource\Pages;

use Liamtseva\Cinema\Filament\Resources\UserListResource;
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
