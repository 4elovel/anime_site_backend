<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use AnimeSite\Filament\Resources\RatingResource\Pages;
use AnimeSite\Filament\Resources\RatingResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Rating;
use AnimeSite\Models\User;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Оцінки';
    protected static ?string $modelLabel = 'Оцінка';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('user_id')
                            ->label('Користувач')
                            ->searchable()
                            ->options(User::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->columnSpan(2),

                Section::make()
                    ->schema([
                        Select::make('anime_id')
                            ->label('Аніме')
                            ->searchable()
                            ->options(Anime::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->columnSpan(2),

                Section::make()
                    ->schema([
                        Select::make('number')
                            ->label('Оцінка')
                            ->options(range(1, 10))
                            ->required(),
                        TextArea::make('review')
                            ->label('Відгук')
                            ->nullable(),
                    ])
                    ->columnSpan(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1, 2, 3 => 'danger',
                        4, 5, 6 => 'warning',
                        7, 8, 9, 10 => 'success',
                        default => 'muted',
                    }),

                TextColumn::make('review')
                    ->label('Відгук')
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('anime_id')
                    ->label('Аніме')
                    ->options(function () {
                        return Anime::pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('Вибрати аніме'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');

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
