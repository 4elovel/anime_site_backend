<?php

namespace Liamtseva\Cinema\Filament\Resources\CommentLikeResource\Pages;

use Liamtseva\Cinema\Filament\Resources\CommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommentLike extends EditRecord
{
    protected static string $resource = CommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
