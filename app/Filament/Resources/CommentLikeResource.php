<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Filament\Resources\CommentLikeResource\Pages;
use Liamtseva\Cinema\Filament\Resources\CommentLikeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Comment;
use Liamtseva\Cinema\Models\CommentLike;
use Liamtseva\Cinema\Models\User;


class CommentLikeResource extends Resource
{
    protected static ?string $model = CommentLike::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center';
    protected static ?string $navigationSubGroup  = 'Коментарі';
    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('comment_id')
                    ->label('Коментар')
                    ->options(Comment::query()->pluck('body', 'id')) // Показує текст коментаря для вибору
                    ->searchable() // Додає поле пошуку
                    ->required(),

                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('comment.body')
                    ->label('Коментар')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Str::limit($state, 60, '...')),
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->sortable()
                    ->searchable(),

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
            'index' => Pages\ListCommentLikes::route('/'),
            'create' => Pages\CreateCommentLike::route('/create'),
            'edit' => Pages\EditCommentLike::route('/{record}/edit'),
        ];
    }
}
