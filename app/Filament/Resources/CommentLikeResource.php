<?php

namespace AnimeSite\Filament\Resources;

use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\CommentLikeResource\Pages;
use AnimeSite\Filament\Resources\CommentLikeResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Comment;
use AnimeSite\Models\CommentLike;
use AnimeSite\Models\User;

class CommentLikeResource extends Resource
{
    protected static ?string $model = CommentLike::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Лайки коментарів';
    protected static ?string $modelLabel = 'Лайк коментаря';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('comment_id')
                    ->label('Коментар')
                    ->options(Comment::query()->pluck('body', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Toggle::make('is_liked')
                    ->label('Це лайк?')
                    ->default(true),
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
                TextColumn::make('comment.body')
                    ->label('Коментар')
                    ->sortable()
                    ->searchable()
                    ->limit(70),
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_liked')
                    ->label('Лайк/Дизлайк')
                    ->boolean(),

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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommentLikes::route('/'),
        ];
    }
}
