<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Closure;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Filament\Resources\StudioResource\Pages;
use Liamtseva\Cinema\Models\Studio;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form->schema([
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

            Section::make('Зображення')
                ->schema([
                    FileUpload::make('image') // Поле для завантаження файлів
                    ->label('Завантажити файл') // Підпис для поля
                    ->image() // Якщо ви хочете, щоб завантажувались тільки зображення
                    ->required() // Якщо це поле обов'язкове
                    ->disk('public') // Диск для збереження файлів (визначається у config/filesystems.php)
                    ->directory('uploads') // Каталог для збереження файлів
                    ->maxSize(10240) // Максимальний розмір файлу в КБ (наприклад, 10 МБ)
                    ->enableDownload(),
                ])->columnSpan(1),

            TextInput::make('description')
                ->required()
                ->maxLength(512),
                ])
                ->columnSpan(2)
                ->columns(2),

            Section::make('Meta')
                ->schema([
            TextInput::make('meta_title')
                ->maxLength(128),
            TextInput::make('meta_description')
                ->maxLength(376),
            TextInput::make('meta_image')
                ->label('Meta image URL')
                ->url()
                ->maxLength(2048),
                    ])->columnSpan(1)
                ->columns(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                ->label('ID')
                ->searchable(),
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            ImageColumn::make('image'),
        ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),

                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Model $record) => route('anime.show', $record)), // Example of a custom route

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
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
        ];
    }
}
