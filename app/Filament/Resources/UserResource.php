<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Liamtseva\Cinema\Enums\Gender;
use Liamtseva\Cinema\Enums\Role;
use Liamtseva\Cinema\Filament\Resources\UserResource\Pages;
use Liamtseva\Cinema\Filament\Resources\UserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Filament\Resources\UserResource\RelationManagers\AchievementsRelationManager;
use Liamtseva\Cinema\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(128)
                    ->unique(ignoreRecord: true),

                TextInput::make('email')
                    ->required()
                    ->maxLength(128)
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->password() // Вказуємо, що це поле для паролю
                    ->required() // Поле є обов'язковим
                    ->minLength(8)
                    ->helperText('Пароль має бути не менше 8 символів.'),

                TextInput::make('description')
                    ->required()
                    ->maxLength(248),

                Select::make('gender')
                    ->label('Gender')
                    ->options(Gender::labels())
                    ->required(),

                Select::make('role')
                    ->label('Role')
                    ->options(Role::labels())
                    ->required(),
                TextInput::make('avatar')
                    ->label('Avatar URL')
                    ->url()
                    ->maxLength(2048),

                TextInput::make('backdrop')
                    ->label('Backdrop URL')
                    ->url()
                    ->maxLength(2048),

                DatePicker::make('birthday')
                    ->label('Birthday')
                    ->nullable(),

                DateTimePicker::make('last_seen_at')
                    ->label('Last seen at')
                    ->nullable(),

                DateTimePicker::make('email_verified_at')
                    ->label('Email verified at')
                    ->nullable(),

                Toggle::make('allow_adult')
                    ->label('Allow adult')
                    ->default(false),

                Toggle::make('is_auto_next')
                    ->label('Is auto next')
                    ->default(false),

                Toggle::make('is_auto_play')
                    ->label('Is auto play')
                    ->default(false),

                Toggle::make('is_auto_skip_intro')
                    ->label('Is auto skip intro')
                    ->default(false),

                Toggle::make('is_private_favorites')
                    ->label('Is private favorites')
                    ->default(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->color(fn ($state) => match($state) {
                        Role::ADMIN => 'danger',
                        Role::MODERATOR => 'warning',
                        Role::USER => 'success',
                        default => 'muted',
                    }),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn ($state) => $state->name()),
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
                DeleteBulkAction::make()
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->before(fn (array $records) => // Логіка перед видаленням
                    collect($records)->filter(fn ($record) => $record->is_published)
                        ->each(fn ($record) => $record->addError('id', 'Cannot delete published records.'))
                    ),
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            AchievementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
