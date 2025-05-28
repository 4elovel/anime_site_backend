<?php

namespace AnimeSite\Filament\Resources\CommentResource\RelationManagers;

use AnimeSite\Models\Comment;
use AnimeSite\Models\CommentLike;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Models\User;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;

class LikesRelationManager extends RelationManager
{
    protected static string $relationship = 'likes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Додати лайк')
                ->form([
                    Select::make('user_id')
                        ->label('Користувач')
                        ->options(User::query()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('comment_id')
                        ->label('Коментар')
                        ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                        ->disabled()
                        ->dehydrated()
                        ->options(Comment::query()->pluck('body', 'id'))
                        ->required(),
                    Checkbox::make('is_liked')
                        ->label('Це лайк?')
                        ->default(true),
                ])
                ->using(function (array $data, RelationManager $livewire): Model {
                    return CommentLike::create([
                        'user_id' => $data['user_id'],
                        'comment_id' => $data['comment_id'],
                        'is_liked' => $data['is_liked'] ?? false,
                    ]);
                }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
