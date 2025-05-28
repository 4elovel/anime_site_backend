<?php

namespace AnimeSite\Filament\Resources\PersonResource\Pages;

use AnimeSite\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;
}
