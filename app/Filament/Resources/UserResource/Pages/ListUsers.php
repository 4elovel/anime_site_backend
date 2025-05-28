<?php

namespace AnimeSite\Filament\Resources\UserResource\Pages;

use AnimeSite\Filament\Resources\UserResource\Widgets\UserCountWidget;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі'),

            'user' => Tab::make('Користувач')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'user')),

            'admin' => Tab::make('Адміністратор')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin')),

            'moderator' => Tab::make('Модератор')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'moderator')),
        ];

    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserCountWidget::make(),
        ];
    }
}
