<?php

namespace Liamtseva\Cinema\Filament\Resources;

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
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Filament\Resources\AchievementResource\Pages;
use Liamtseva\Cinema\Filament\Resources\AchievementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Achievement;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(128)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(128)
                    ->unique(ignoreRecord: true),

                TextInput::make('description')
                    ->required()
                    ->maxLength(512),

                        TextInput::make('max_counts')
                            ->label('Максимальна кількість')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)

            ])
            ->columnSpan(2)
            ->columns(2),

                Section::make('Зображення')
                    ->schema([
                        FileUpload::make('icon') // Поле для завантаження файлів
                        ->label('Завантажити файл') // Підпис для поля
                        ->image() // Якщо ви хочете, щоб завантажувались тільки зображення
                        ->disk('public') // Диск для збереження файлів (визначається у config/filesystems.php)
                        ->directory('uploads') // Каталог для збереження файлів
                        ->maxSize(10240) // Максимальний розмір файлу в КБ (наприклад, 10 МБ)
                        ->enableDownload(),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('max_counts')
                    ->label('Число') // Назва колонки
                    ->sortable() // Додає можливість сортування
                    ->searchable() // Додає можливість пошуку
                    ->color(fn ($state) => match (true) { // Додає кольорове форматування залежно від значення
                        $state < 10 => 'danger', // Червоний для значень менше 10
                        $state < 50 => 'warning', // Жовтий для значень від 10 до 49
                        $state >= 50 => 'success', // Зелений для значень 50 і більше
                        default => 'muted',
                    }),
            ])
            ->filters([
                //
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
                    ->modalHeading('Are you sure you want to delete this record?')
                    ->modalSubheading('This action cannot be undone.')
                    ->action(fn (Model $record) => $record->delete()),

            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->before(fn (array $records) => // Логіка перед видаленням
                    collect($records)->filter(fn ($record) => $record->is_published)
                        ->each(fn ($record) => $record->addError('id', 'Cannot delete published records.'))
                    ),
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
