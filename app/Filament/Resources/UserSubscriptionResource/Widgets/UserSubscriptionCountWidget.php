<?php

namespace AnimeSite\Filament\Resources\UserSubscriptionResource\Widgets;

use AnimeSite\Models\UserSubscription;
use Filament\Forms\Components\Card;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserSubscriptionCountWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Загальна кількість підписок', UserSubscription::count())
                ->icon('heroicon-o-credit-card'),
        ];
    }
}
