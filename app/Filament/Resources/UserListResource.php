<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Liamtseva\Cinema\Enums\UserListType;
use Liamtseva\Cinema\Filament\Resources\UserListResource\Pages;
use Liamtseva\Cinema\Filament\Resources\UserListResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Models\Person;
use Liamtseva\Cinema\Models\Selection;
use Liamtseva\Cinema\Models\Tag;
use Liamtseva\Cinema\Models\User;
use Liamtseva\Cinema\Models\UserList;

class UserListResource extends Resource
{
    protected static ?string $model = UserList::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(), // Пошук по користувачам

                MorphToSelect::make('listable')
                    ->label('Пов’язаний об’єкт')
                    ->searchable()
                    ->required()
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

                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options(UserListType::labels())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('user_id')
                    ->label('Користувач')
                    ->sortable(),

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
                    ->formatStateUsing(fn ($state) => substr($state, 24)),

                TextColumn::make('listable_id')
                    ->label('ID об’єкта')
                    ->sortable(),

                TextColumn::make('type')
                    ->sortable()
                    ->label('Тип')
                    ->color(fn ($state) => match ($state) {
                        UserListType::NOT_WATCHING => 'primary',
                        UserListType::STOPPED => 'secondary',
                        UserListType::WATCHED => 'warning',
                        UserListType::PLANNED => 'info',
                        UserListType::FAVORITE => 'danger',
                        UserListType::WATCHING => 'success',
                        default => 'muted',
                    })
                    ->formatStateUsing(fn ($state) => $state->name()),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options(UserListType::labels()),
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
            'index' => Pages\ListUserLists::route('/'),
            'create' => Pages\CreateUserList::route('/create'),
            'edit' => Pages\EditUserList::route('/{record}/edit'),
        ];
    }
}
