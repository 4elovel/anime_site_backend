<?php

namespace AnimeSite\Filament\Resources\UserSubscriptionResource\Pages;

use AnimeSite\Filament\Resources\UserSubscriptionResource;
use AnimeSite\Filament\Resources\UserSubscriptionResource\Widgets\UserSubscriptionCountWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListUserSubscriptions extends ListRecords
{
    protected static string $resource = UserSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі'),

            'active' => Tab::make('Активні')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),

            'inactive' => Tab::make('Неактивні')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }

}
