<?php

namespace AnimeSite\Filament\Resources\EpisodeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\User;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('body')
                    ->label('Текст коментаря')
                    ->required()
                    ->maxLength(1000),

                Checkbox::make('is_spoiler')
                    ->label('Спойлер')
                    ->default(false),

                Select::make('parent_id')
                    ->label('Батьківський коментар')
                    ->relationship('parent', 'body')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('body')
                    ->label('Текст')
                    ->limit(50)
                    ->searchable(),

                IconColumn::make('is_spoiler')
                    ->label('Спойлер')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('likes_count')
                    ->label('Лайки')
                    ->counts('likes')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('root_comments')
                    ->label('Тільки кореневі коментарі')
                    ->query(fn (Builder $query) => $query->whereNull('parent_id')),

                Tables\Filters\Filter::make('replies')
                    ->label('Тільки відповіді')
                    ->query(fn (Builder $query) => $query->whereNotNull('parent_id')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати коментар')
                    ->form([
                        Select::make('user_id')
                            ->label('Користувач')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('body')
                            ->label('Текст коментаря')
                            ->required()
                            ->maxLength(1000),

                        Checkbox::make('is_spoiler')
                            ->label('Спойлер')
                            ->default(false),

                        Select::make('commentable_id')
                            ->label('Епізод')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(Episode::query()->pluck('name', 'id'))
                            ->required(),

                        Select::make('commentable_type')
                            ->label('Тип')
                            ->default(Episode::class)
                            ->disabled()
                            ->dehydrated()
                            ->options([Episode::class => 'Episode'])
                            ->required(),

                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        return Comment::create([
                            'user_id' => $data['user_id'],
                            'body' => $data['body'],
                            'is_spoiler' => $data['is_spoiler'] ?? false,
                            'commentable_id' => $data['commentable_id'],
                            'commentable_type' => $data['commentable_type'],
                        ]);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
