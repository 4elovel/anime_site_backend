<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\SelectionResource\Pages;
use AnimeSite\Filament\Resources\SelectionResource\RelationManagers\AnimesRelationManager;
use AnimeSite\Filament\Resources\SelectionResource\RelationManagers\EpisodesRelationManager;
use AnimeSite\Filament\Resources\SelectionResource\RelationManagers\PersonsRelationManager;
use AnimeSite\Filament\Resources\SelectionResource\RelationManagers\TagsRelationManager;
use AnimeSite\Filament\Resources\SelectionResource\RelationManagers\UserListsRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Person;
use AnimeSite\Models\Selection;
use AnimeSite\Models\User;

class SelectionResource extends Resource
{
    protected static ?string $model = Selection::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Добірки';
    protected static ?string $modelLabel = 'Добірка';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Назва')
                                    ->required()
                                    ->maxLength(128)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Selection::generateSlug($state));
                                        $set('meta_title', Selection::makeMetaTitle($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(128)
                                    ->unique(ignoreRecord: true),

                                Select::make('user_id')
                                    ->label('Користувач')
                                    ->options(User::query()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),

                                Toggle::make('is_published')
                                    ->label('Опубліковано')
                                    ->default(true),

                                Toggle::make('is_active')
                                    ->label('Активно')
                                    ->default(true),
                            ])
                            ->columns(3),

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
                                        $set('meta_description', Selection::makeMetaDescription($plainText));
                                    }),


                            ])
                            ->columnSpan(3),

                        Section::make()
                            ->schema([
                                FileUpload::make('poster')
                                    ->label('Постер')
                                    ->image()
                                    ->directory('selections')
                                    ->maxSize(5120)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(3),

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

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('poster')
                    ->label('Постер')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Опубліковано')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_published')
                    ->label('Опубліковані')
                    ->query(fn (Builder $query) => $query->where('is_published', true)),


                SelectFilter::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            AnimesRelationManager::class,
            PersonsRelationManager::class,
            EpisodesRelationManager::class,
            TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSelections::route('/'),
            'create' => Pages\CreateSelection::route('/create'),
            'edit' => Pages\EditSelection::route('/{record}/edit'),
        ];
    }
}
