<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Filament\Resources\CommentResource\Pages;
use Liamtseva\Cinema\Filament\Resources\CommentResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Comment;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Models\Selection;
use Liamtseva\Cinema\Models\UserList;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';
    protected static ?string $navigationSubGroup  = 'Коментарі';
    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                MorphToSelect::make('commentable')
                    ->label('Пов’язаний об’єкт')
                    ->searchable()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Episode::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Anime::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Selection::class)
                            ->titleAttribute('name'),
                    ]),

                Select::make('parent_id')
                    ->label('Батьківський коментар')
                    ->relationship('parent', 'body')
                    ->nullable()
                    ->searchable(),

                Select::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\TextInput::make('body')
                    ->label('Текст коментаря')
                    ->required(),

                Checkbox::make('is_spoiler')
                    ->label('Спойлер')
                    ->default(false),

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
                    ->sortable()
                    ->searchable(),

                TextColumn::make('commentable_type')
                    ->label('Тип')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => substr($state, 24)),

                TextColumn::make('commentable_id')
                    ->label('ID об’єкта')
                    ->sortable(),

                TextColumn::make('body')
                    ->label('Текст коментаря')
                    ->sortable()
                    ->limit(28),

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
                // Масова дія для видалення, яка також підтверджує перед виконанням
                DeleteBulkAction::make()
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->before(fn (array $records) => // Логіка перед видаленням
                    collect($records)->filter(fn ($record) => $record->is_published)
                        ->each(fn ($record) => $record->addError('id', 'Cannot delete published records.'))
                    ),
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
