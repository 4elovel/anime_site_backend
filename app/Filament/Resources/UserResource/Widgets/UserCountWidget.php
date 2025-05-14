<?php

namespace AnimeSite\Filament\Resources\UserResource\Widgets;

use AnimeSite\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserCountWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Загальна кількість користувачів', User::count())
                ->icon('heroicon-o-users'),
        ];
    }
}
