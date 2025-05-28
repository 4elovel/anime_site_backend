<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\AchievementResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Achievement;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $modelLabel = 'Досягнення';
    protected static ?string $pluralModelLabel = 'Досягнення';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([

                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Назва') // Label for the name field
                                    ->required()
                                    ->maxLength(128)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Achievement::generateSlug($state)); // Auto-generate slug from name
                                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(128)
                    ->unique(ignoreRecord: true),

                TextInput::make('description')
                    ->required()
                    ->maxLength(512),

                        Section::make()
                            ->schema([
                                TextInput::make('max_counts')
                                    ->label('Максимальна кількість') // Label for max count field
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(100),
                            ])
                            ->columnSpan(3),
                        // Section for 'icon' and 'max_counts' on a single row
                        Section::make()
                            ->schema([
                                FileUpload::make('icon')
                                    ->label('Зображення')
                                    ->image() // Restrict to images only
                                    ->directory('public/icon_achievements') // Define upload directory
                                    ->maxSize(10240) // Limit file size to 10MB
                                    ->enableDownload(),
                            ])
                            ->columnSpan(3),


                    ])

            ])]);
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
                TextColumn::make('slug')
                    ->label(('Slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Опис')
                    ->searchable()
                    ->sortable()
                    ->limit(60),

                ImageColumn::make('icon')
                ->label('Зображення')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('max_counts')
                    ->label('Число')
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
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->bulkActions([
            ]);
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
            'index' => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit' => Pages\EditAchievement::route('/{record}/edit'),
        ];
    }
}
