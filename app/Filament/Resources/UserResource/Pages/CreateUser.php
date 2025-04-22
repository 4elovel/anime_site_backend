<?php

namespace Liamtseva\Cinema\Filament\Resources\UserResource\Pages;

use Liamtseva\Cinema\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
