<?php

namespace Liamtseva\Cinema\Filament\Resources\CommentResource\Pages;

use Liamtseva\Cinema\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;
}
