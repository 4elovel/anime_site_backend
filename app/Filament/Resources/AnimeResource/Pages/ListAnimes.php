<?php

namespace AnimeSite\Filament\Resources\AnimeResource\Pages;

use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\Kind;
use AnimeSite\Filament\Resources\AnimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnimes extends ListRecords
{
    protected static string $resource = AnimeResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі'),

            'tv_series' => Tab::make('Аніме серіал')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kind', Kind::TV_SERIES->value)),

            'tv_special' => Tab::make('Спеціальний випуск аніме')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kind', Kind::TV_SPECIAL->value)),

            'full_length' => Tab::make('Повнометражний аніме фільм')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kind', Kind::FULL_LENGTH->value)),

            'short_film' => Tab::make('Короткометражний аніме фільм')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kind', Kind::SHORT_FILM->value)),

            'ova' => Tab::make('Оригінальна відео-анімація (OVA)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kind', Kind::OVA->value)),

            'ona' => Tab::make('Оригінальна інтернет-анімація (ONA)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kind', Kind::ONA->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function GetHeaderWidgets(): array
    {
        return [
            AnimeResource\Widgets\AnimeCountWidget::class,
        ];
    }
}
