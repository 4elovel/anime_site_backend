<?php

namespace AnimeSite\Filament\Resources\UserListResource\Pages;

use Filament\Resources\Components\Tab;
use AnimeSite\Enums\UserListType;
use AnimeSite\Filament\Resources\UserListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUserLists extends ListRecords
{
    protected static string $resource = UserListResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі'),
            'favorite' => Tab::make('Улюблене')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::FAVORITE->value)),
            'watching' => Tab::make('Дивлюся')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::WATCHING->value)),
            'watched' => Tab::make('Переглянуто')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::WATCHED->value)),
            'planned' => Tab::make('В планах')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::PLANNED->value)),
            'not watching' => Tab::make('Не дивлюся')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::NOT_WATCHING->value)),
            'rewatching' => Tab::make('Передивляюся')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::REWATCHING->value)),
            'stopped' => Tab::make('Перестав')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', UserListType::STOPPED->value)),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
