<?php

namespace AnimeSite\Filament\Resources;

use Closure;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
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
use AnimeSite\Filament\Resources\StudioResource\Pages;
use AnimeSite\Filament\Resources\StudioResource\RelationManagers\AnimesRelationManager;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $pluralModelLabel = 'Студії';
    protected static ?string $modelLabel = 'Студія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            Section::make()
                ->schema([
                    // Name Input
                    TextInput::make('name')
                        ->label('Ім\'я')
                        ->required()
                        ->maxLength(128)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                            if ($operation == 'edit' || empty($state)) {
                                return;
                            }
                            $set('slug', Studio::generateSlug($state));
                            $set('meta_title', Tag::makeMetaTitle($state));
                        }),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(128)
                        ->unique(ignoreRecord: true),
                ])
                ->columnSpan(2)
                ->columns(2),

            // Image Section
            Section::make()
                ->schema([
                    // Image Upload
                    FileUpload::make('image')
                        ->label('Зображення')
                        ->image()
                        ->directory('public/studio')
                        ->maxSize(10240)
                        ->enableDownload()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (!empty($state)) {
                                $set('meta_image', $state);
                            }
                        }),
                ])
                ->columnSpan(2),

            // Description Section
            Section::make()
                ->schema([
                    // Description Input with RichEditor
                    RichEditor::make('description')
                        ->label('Опис')
                        ->maxLength(512)
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'h4', 'bulletList', 'orderedList',
                            'link', 'blockquote', 'codeBlock', 'undo', 'redo',
                        ])
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                            if ($operation == 'edit' || empty($state)) {
                                return;
                            }
                            $plainText = strip_tags($state);
                            $set('meta_description', Studio::makeMetaDescription($plainText));
                        }),
                ])
                ->columnSpan(3),

            // SEO Settings Section
            Section::make(__('SEO Налаштування'))
                ->collapsible()
                ->collapsed()
                ->schema([
                    // Meta Title
                    TextInput::make('meta_title')
                        ->maxLength(128)
                        ->label(__('Meta заголовок')),

                    // Meta Description
                    TextInput::make('meta_description')
                        ->maxLength(376)
                        ->label(__('Meta опис')),

                    // Meta Image
                    FileUpload::make('meta_image')
                        ->image()
                        ->directory('public/meta')
                        ->label(__('Meta зображення')),
                ])
                ->columnSpan(3)
        ]);

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
            TextColumn::make('name')
                ->label('Назва')
                ->searchable()
                ->sortable(),
            TextColumn::make('description')
                ->label(__('Опис'))
                ->searchable()
                ->limit(80),

            TextColumn::make('slug')
                ->label(('Slug'))
                ->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('image')
                ->label('Зображення')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('meta_title')
                ->label(('Meta загаловок'))
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('meta_description')
                ->label(__('Meta опис'))
                ->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('meta_image')
                ->label('Meta зображення')
                ->toggleable(isToggledHiddenByDefault: true),
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
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');

    }

    public static function getRelations(): array
    {
        return [
            AnimesRelationManager::class,
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
