<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\CommentResource\Pages;
use AnimeSite\Filament\Resources\CommentResource\RelationManagers;
use AnimeSite\Filament\Resources\CommentResource\RelationManagers\ChildrenRelationManager;
use AnimeSite\Filament\Resources\CommentResource\RelationManagers\LikesRelationManager;
use AnimeSite\Filament\Resources\CommentResource\RelationManagers\ReportsRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Selection;
use AnimeSite\Models\UserList;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Коментарі';
    protected static ?string $modelLabel = 'Коментар';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        MorphToSelect::make('commentable')
                            ->label('Пов’язаний об’єкт')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->types([
                                MorphToSelect\Type::make(Episode::class)
                                    ->titleAttribute('name'),
                                MorphToSelect\Type::make(Anime::class)
                                    ->titleAttribute('name'),
                                MorphToSelect\Type::make(Selection::class)
                                    ->titleAttribute('name'),
                                MorphToSelect\Type::make(Comment::class)
                                    ->titleAttribute('body'),
                            ]),
                    ])
                    ->columnSpan(3),
                Section::make()
                    ->schema([
                        Select::make('user_id')
                            ->label('Користувач')
                            ->searchable()
                            ->preload()
                            ->relationship('user', 'name')
                            ->required(),

                        TextInput::make('body')
                            ->label('Текст коментаря')
                            ->required(),
                    ])
                    ->columnSpan(2),
                Section::make()
                    ->schema([
                        Checkbox::make('is_spoiler')
                            ->label('Спойлер')
                            ->default(false),
                    ])
                    ->columnSpan(2),
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
                    ->sortable()
                    ->searchable(),

                TextColumn::make('commentable_type')
                    ->label('Тип')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        Anime::class => 'danger',
                        Episode::class => 'warning',
                        Comment::class=> 'primary',
                        Selection::class=> 'success',
                        default => 'muted',
                    })
                    ->formatStateUsing(function ($state) {
                        return class_basename($state);
                    }),

                TextColumn::make('commentable_id')
                    ->label('Об’єкт')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $modelClass = $record->commentable_type;
                        $modelInstance = $modelClass::find($state);
                        if ($modelInstance) {
                            if ($modelClass === Comment::class) {
                                return $modelInstance->body;
                            }
                            return $modelInstance->name;
                        }
                        return 'N/A';
                    })
                    ->searchable()
                    ->limit(25),

                TextColumn::make('body')
                    ->label('Текст коментаря')
                    ->sortable()
                    ->searchable()
                    ->limit(50),

                IconColumn::make('is_spoiler')
                    ->label('Спойлер')
                    ->boolean(),

            ])
            ->filters([
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
            LikesRelationManager::class,
            ReportsRelationManager::class,
            ChildrenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
