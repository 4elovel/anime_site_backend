<?php

namespace AnimeSite\Filament\Resources;

use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\AchievementUserResource\Pages;
use AnimeSite\Filament\Resources\AchievementUserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\AchievementUser;
use AnimeSite\Models\Comment;
use AnimeSite\Models\User;

class AchievementUserResource extends Resource
{
    protected static ?string $model = AchievementUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Досягнення користувачів';
    protected static ?string $modelLabel = 'Досягнення користувача';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('achievement_id')
                    ->label('Досягення')
                    ->options(Achievement::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('progress_count')
                    ->label('Прогрес')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(100),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('achievement.name')
                    ->label('Досягнення')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('progress_count')
                    ->label('Прогрес')
                    ->sortable()
                    ->badge()
                    ->searchable()
                    ->color(fn($state) => match (true) {
                        $state < 10 => 'danger',
                        $state < 50 => 'warning',
                        $state >= 50 => 'success',
                        default => 'muted',
                    }),
            ])
            ->filters([
                SelectFilter::make('achievement_id')
                    ->label('Досягення')
                    ->options(function () {
                        return Achievement::query()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('Вибрати досягнення'),

                SelectFilter::make('user_id')
                    ->label('Користувач')
                    ->options(function () {
                        return User::query()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('Вибрати користувача'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAchievementUsers::route('/'),
        ];
    }
}
