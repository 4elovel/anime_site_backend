<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\TagResource\Pages;
use AnimeSite\Filament\Resources\TagResource\RelationManagers\AnimesRelationManager;
use AnimeSite\Models\Tag;
use PhpParser\Node\Stmt\Label;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $pluralModelLabel = 'Теги';
    protected static ?string $modelLabel = 'Тег';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Basic Information Section
                Section::make()
                    ->schema([
                        // Name Input
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Tag::generateSlug($state));
                                $set('meta_title', Tag::makeMetaTitle($state));
                            }),

                        // Slug Input
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                // Description Section
                Section::make()
                    ->schema([
                        // Rich Text Editor for Description
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
                                $set('meta_description', Tag::makeMetaDescription($plainText));
                            }),


                    ])
                    ->columnSpan(3),



                // Additional Information Section
                Section::make()
                    ->schema([
                        // Parent Tag Selection
                        Select::make('parent_id')
                            ->label('Батьківський тег')
                            ->relationship('parent', 'name')
                            ->nullable(),
                        TagsInput::make('aliases')
                            ->label('Псевдоніми')
                            ->required(),
                        // Toggle for Genre

                        Toggle::make('is_genre')
                            ->label('Це жанр?')
                            ->default(false),


                    ])
                    ->columnSpan(2)
                    ->columns(3),

                // Image Section
                Section::make()
                    ->schema([
                        // Image Upload
                        FileUpload::make('image')
                            ->label('Зображення')
                            ->image()
                            ->directory('public/tag')
                            ->maxSize(10240)
                            ->enableDownload()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (!empty($state)) {
                                    $set('meta_image', $state);
                                }
                            }),
                    ])
                    ->columnSpan(2),

                // SEO Settings Section
                Section::make(__('SEO Налаштування'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // Meta Title Input
                        TextInput::make('meta_title')
                            ->maxLength(128)
                            ->label(__('Meta заголовок')),

                        // Meta Description Input
                        TextInput::make('meta_description')
                            ->maxLength(376)
                            ->label(__('Meta опис')),

                        // Meta Image Upload
                        FileUpload::make('meta_image')
                            ->image()
                            ->directory('public/meta')
                            ->label(__('Meta зображення')),
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
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(80),
                IconColumn::make('is_genre')
                    ->label('Жанр')
                    ->boolean(),
                ImageColumn::make('image')
                    ->label('Зображення'),

                TextColumn::make('aliases')
                    ->label('Псевдоніми')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slug')
                    ->label(('Slug'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parent.name')
                    ->label('Батьківський тег')
                    ->sortable()
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
