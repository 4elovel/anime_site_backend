<?php

namespace AnimeSite\Filament\Resources\AnimeResource\Widgets;

use AnimeSite\Models\Anime;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnimeCountWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Загальна кількість аніме', Anime::count())
                ->icon('heroicon-o-film'),
        ];
    }
}
