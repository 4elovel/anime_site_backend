<?php

namespace Liamtseva\Cinema\Filament\Resources\CommentResource\Pages;

use Liamtseva\Cinema\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
