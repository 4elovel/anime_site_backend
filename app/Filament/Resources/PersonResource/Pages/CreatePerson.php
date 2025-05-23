<?php

namespace Liamtseva\Cinema\Filament\Resources\PersonResource\Pages;

use Liamtseva\Cinema\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;
}
