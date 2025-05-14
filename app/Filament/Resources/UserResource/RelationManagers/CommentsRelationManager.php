<?php

namespace AnimeSite\Filament\Resources\UserResource\RelationManagers;

use AnimeSite\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Section;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Selection;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('commentable_type')
                    ->label('Тип')
                    ->sortable()
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
                    ->limit(30),

                TextColumn::make('body')
                    ->label('Текст коментаря')
                    ->sortable()
                    ->searchable()
                    ->limit(70),

                IconColumn::make('is_spoiler')
                    ->label('Спойлер')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати коментар')
                ->form([
                    Select::make('user_id')
                        ->label('Користувач')
                        ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                        ->disabled()
                        ->dehydrated()
                        ->options(User::query()->pluck('name', 'id'))
                        ->required(),

                    TextInput::make('body')
                        ->label('Текст коментаря')
                        ->required()
                        ->maxLength(1000),

                    Checkbox::make('is_spoiler')
                        ->label('Спойлер')
                        ->default(false),
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
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');

    }
}
