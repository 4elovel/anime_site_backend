<?php

namespace AnimeSite\Filament\Resources\SelectionResource\RelationManagers;

use AnimeSite\Models\Selection;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Episode;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'episodes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('anime.name')
                    ->label('Аніме')
                    ->sortable(),

                TextColumn::make('number')
                    ->label('Номер')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Назва'),

                TextColumn::make('description')
                    ->label('Опис')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->toggleable(),

                TextColumn::make('duration')
                    ->label('Тривалість (хв)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('air_date')
                    ->label('Дата виходу')
                    ->sortable()
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),

                BooleanColumn::make('is_filler')
                    ->label('Філер')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pictures')
                    ->label('Зображення')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('meta_title')
                    ->label('Meta заголовок')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_description')
                    ->label('Meta опис')
                    ->limit(100)
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('meta_image')
                    ->label('Meta зображення')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->sortable()
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->sortable()
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('aired')
                    ->label('Вийшли в ефір')
                    ->query(fn (Builder $query) => $query->whereNotNull('air_date')->where('air_date', '<=', now())),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Очікуються')
                    ->query(fn (Builder $query) => $query->where('air_date', '>', now())),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати епізод')
                    ->form([
                        Select::make('episode_id')
                            ->label('Епізод')
                            ->options(Episode::query()->with('anime')->get()->mapWithKeys(function ($episode) {
                                return [$episode->id => "{$episode->anime->name} - Епізод {$episode->number}"];
                            }))
                            ->searchable()
                            ->required(),
                        Select::make('selection_id')
                            ->label('Добірка')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(Selection ::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('selectionables')->insert([
                            'selection_id' => $data['selection_id'],
                            'selectionable_id' => $data['episode_id'],
                            'selectionable_type' => Episode::class,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        return Episode::find($data['episode_id']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('air_date', 'desc');
    }
}
