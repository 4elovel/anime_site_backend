<?php

namespace AnimeSite\Filament\Resources\PersonResource\Pages;

use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\PersonType;
use AnimeSite\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeople extends ListRecords
{
    protected static string $resource = PersonResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі'),

            'character' => Tab::make('Персонаж')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::CHARACTER->value)),

            'voice_actor' => Tab::make('Актор озвучення')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::VOICE_ACTOR->value)),

            'director' => Tab::make('Режисер')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::DIRECTOR->value)),

            'producer' => Tab::make('Продюсер')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::PRODUCER->value)),

            'scriptwriter' => Tab::make('Сценарист')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::SCRIPTWRITER->value)),

            'character_designer' => Tab::make('Дизайнер персонажів')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::CHARACTER_DESIGNER->value)),

            'animation_director' => Tab::make('Директор анімації')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::ANIMATION_DIRECTOR->value)),

            'key_animator' => Tab::make('Ключовий аніматор')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::KEY_ANIMATOR->value)),

            'inbetween_animator' => Tab::make('Проміжний аніматор')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::INBETWEEN_ANIMATOR->value)),

            'background_artist' => Tab::make('Художник фону')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::BACKGROUND_ARTIST->value)),

            'color_designer' => Tab::make('Дизайнер кольору')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::COLOR_DESIGNER->value)),

            'sound_director' => Tab::make('Звуковий режисер')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::SOUND_DIRECTOR->value)),

            'music_composer' => Tab::make('Композитор')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::MUSIC_COMPOSER->value)),

            'editor' => Tab::make('Монтажер')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::EDITOR->value)),

            'cgi_artist' => Tab::make('CGI-художник')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', PersonType::CGI_ARTIST->value)),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
