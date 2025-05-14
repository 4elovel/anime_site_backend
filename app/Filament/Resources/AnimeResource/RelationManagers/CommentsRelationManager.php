<?php

namespace AnimeSite\Filament\Resources\AnimeResource\RelationManagers;

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
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Selection;
use AnimeSite\Models\User;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $label = 'Коментар';      // Однина
    protected static ?string $pluralLabel = 'Коментарі'; // Множина

    public function form(Form $form): Form
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
                    ->searchable(),

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
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('body')
                    ->label('Текст коментаря')
                    ->sortable()
                    ->searchable()
                    ->limit(100),

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
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('body')
                            ->label('Текст коментаря')
                            ->required(),

                        Checkbox::make('is_spoiler')
                            ->label('Спойлер')
                            ->default(false),

                        Select::make('commentable_id')
                            ->label('Об\'єкт')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->options(Anime::query()->pluck('name', 'id'))
                            ->dehydrated()
                            ->required(),

                        Select::make('commentable_type')
                            ->label('Тип')
                            ->default(Anime::class)
                            ->disabled()
                            ->dehydrated()
                            ->options([Anime::class => 'Anime'])
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
                DetachAction::make(),
            ])
            ->bulkActions([
                DetachBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');

    }
}
