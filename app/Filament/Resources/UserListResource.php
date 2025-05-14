<?php

namespace AnimeSite\Filament\Resources;

use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use AnimeSite\Enums\UserListType;
use AnimeSite\Filament\Resources\UserListResource\Pages;
use AnimeSite\Filament\Resources\UserListResource\RelationManagers;

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
use AnimeSite\Models\Person;
use AnimeSite\Models\Selection;
use AnimeSite\Models\Tag;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

class UserListResource extends Resource
{
    protected static ?string $model = UserList::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Списки користувачів';
    protected static ?string $modelLabel = 'Список користувача';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('user_id')
                            ->label('Користувач')
                            ->options(User::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columnSpan(2),

                Section::make()
                    ->schema([
                        MorphToSelect::make('listable')
                            ->label('Пов’язаний об’єкт')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->types([
                                Type::make(Episode::class)
                                    ->titleAttribute('name'),
                                Type::make(Anime::class)
                                    ->titleAttribute('name'),
                                Type::make(Selection::class)
                                    ->titleAttribute('name'),
                                Type::make(Tag::class)
                                    ->titleAttribute('name'),
                                Type::make(Person::class)
                                    ->titleAttribute('name'),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make()
                    ->schema([
                        Select::make('type')
                            ->label('Тип')
                            ->options(UserListType::labels())
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('listable_type')
                    ->label('Тип об’єкта')
                    ->sortable()
                    ->color(fn ($state) => match ($state) {
                        Anime::class => 'danger',
                        Episode::class => 'warning',
                        Person::class=> 'primary',
                        Tag::class=> 'info',
                        Selection::class=> 'success',
                        default => 'muted',
                    })
                    ->formatStateUsing(function ($state) {
                        return class_basename($state);
                    })
                    ->badge()
                    ->icon(fn ($state) => match ($state) {
                        Anime::class => 'heroicon-s-film',
                        Episode::class => 'heroicon-s-rectangle-stack',
                        Person::class=> 'heroicon-s-user-group',
                        Tag::class=> 'heroicon-s-hashtag',
                        Selection::class=> 'heroicon-s-queue-list',
                        default => 'heroicon-s-film'}),

                TextColumn::make('listable_id')
                    ->label('Назва об’єкта')
                    ->formatStateUsing(function ($state, $record) {
                        $modelClass = $record->listable_type;
                        $modelInstance = $modelClass::find($state);
                        if ($modelInstance) {
                            return $modelInstance->name;
                        }
                        return 'N/A';
                    })
                    ->sortable(),

                TextColumn::make('type')
                    ->sortable()
                    ->label('Тип')
                    ->color(fn (UserListType $state): string => $state->getBadgeColor())
                    ->icon(fn (UserListType $state) => $state->getIcon())
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->name()),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserLists::route('/'),
            'create' => Pages\CreateUserList::route('/create'),
            'edit' => Pages\EditUserList::route('/{record}/edit'),
        ];
    }
}
