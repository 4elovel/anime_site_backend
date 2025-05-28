<?php

namespace AnimeSite\Filament\Resources\CommentLikeResource\Pages;

use AnimeSite\Filament\Resources\CommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommentLikes extends ManageRecords
{
    protected static string $resource = CommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
