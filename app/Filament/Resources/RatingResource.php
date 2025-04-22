<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Liamtseva\Cinema\Filament\Resources\RatingResource\Pages;
use Liamtseva\Cinema\Filament\Resources\RatingResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Rating;
use Liamtseva\Cinema\Models\User;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->required(),

                // Вибір аніме
                Select::make('anime_id')
                    ->label('Аніме')
                    ->options(Anime::query()->pluck('name', 'id'))
                    ->required(),

                // Оцінка
                Select::make('number')
                    ->label('Оцінка')
                    ->options(range(1, 10)) // Оцінка від 1 до 10
                    ->required(),

                // Відгук
                TextArea::make('review')
                    ->label('Відгук')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->sortable(),

                TextColumn::make('anime.name')
                    ->label('Аніме')
                    ->sortable()
                    ->limit(20),

                TextColumn::make('number')
                    ->label('Оцінка')
                    ->sortable()
                    ->color(fn ($state) => match ($state) {
                        1, 2, 3 => 'danger', // Червоний для низьких оцінок
                        4, 5, 6 => 'warning', // Жовтий для середніх оцінок
                        7, 8, 9, 10 => 'success', // Зелений для високих оцінок
                        default => 'muted', // Сірий для інших випадків
                    }),

                TextColumn::make('review')
                    ->label('Відгук')
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('anime_id')
                    ->label('Аніме')
                    ->options(function () {
                        // Повертає список всіх аніме, де 'id' є значенням, а 'name' — назвою
                        return Anime::pluck('name', 'id');
                    })
                    ->searchable() // Додає можливість пошуку
                    ->placeholder('Вибрати аніме'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),

                // Додавання дії "Видалити"
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->modalHeading('Are you sure you want to delete this record?') // Заголовок модального вікна
                    ->modalSubheading('This action cannot be undone.') // Текст у модальному вікні
                    ->action(fn (Model $record) => $record->delete()),
            ])
            ->bulkActions([
                // Масова дія для видалення, яка також підтверджує перед виконанням
                DeleteBulkAction::make()
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->defaultSort('created_at', 'desc'); // Default sort by creation date, descending

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
