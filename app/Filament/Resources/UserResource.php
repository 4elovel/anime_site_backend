<?php

namespace AnimeSite\Filament\Resources;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\Role;
use AnimeSite\Filament\Resources\UserResource\Pages;
use AnimeSite\Filament\Resources\UserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Filament\Resources\UserResource\RelationManagers\AchievementsUserRelationManager;
use AnimeSite\Filament\Resources\UserResource\RelationManagers\CommentsRelationManager;
use AnimeSite\Filament\Resources\UserResource\RelationManagers\PaymentsRelationManager;
use AnimeSite\Filament\Resources\UserResource\RelationManagers\SubscriptionsRelationManager;
use AnimeSite\Filament\Resources\UserResource\RelationManagers\WatchHistoriesRelationManager;
use AnimeSite\Filament\Resources\UserResource\RelationManagers\SearchHistoriesRelationManager;
use AnimeSite\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Користувачі';
    protected static ?string $modelLabel = 'Користувач';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ім\'я')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),

                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->helperText('Пароль має бути не менше 8 символів.'),
                    ]),

                Section::make('Додаткова інформація')
                    ->schema([
                        TextInput::make('description')
                            ->label('Опис')
                            ->maxLength(248),

                        Select::make('gender')
                            ->label('Стать')
                            ->options(Gender::labels())
                            ->required(),

                        Select::make('role')
                            ->label('Роль')
                            ->options(Role::labels())
                            ->required(),

                        DatePicker::make('birthday')
                            ->label('Дата народження')
                            ->nullable(),

                        DateTimePicker::make('last_seen_at')
                            ->label('Останній вхід')
                            ->nullable(),

                        DateTimePicker::make('email_verified_at')
                            ->label('Підтвердження email')
                            ->nullable(),
                    ]),

                Section::make('Зображення')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Аватар')
                            ->image()
                            ->directory('public/avatar_users')
                            ->maxSize(10240)
                            ->enableDownload(),
                        FileUpload::make('backdrop')
                            ->label('Фон')
                            ->image()
                            ->directory('public/backdrop_users')
                            ->maxSize(10240)
                            ->enableDownload(),
                    ])
                    ->columns(2),

                Section::make('Налаштування')
                    ->schema([
                        Toggle::make('allow_adult')
                            ->label('Дозволити дорослий контент')
                            ->default(false),

                        Toggle::make('is_auto_next')
                            ->label('Автоматично наступне')
                            ->default(false),

                        Toggle::make('is_auto_play')
                            ->label('Автовідтворення')
                            ->default(false),

                        Toggle::make('is_auto_skip_intro')
                            ->label('Пропуск вступу')
                            ->default(false),

                        Toggle::make('is_private_favorites')
                            ->label('Приватні улюблені')
                            ->default(false),

                    ]),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Ім\'я')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Роль')
                    ->badge()
                    ->color(fn (Role $state): string => $state->getBadgeColor())
                    ->icon(fn (Role $state) => $state->getIcon())
                    ->formatStateUsing(fn ($state) => $state->name()),
                TextColumn::make('gender')
                    ->label('Стать')
                    ->badge()
                    ->color(fn (Gender $state): string => $state->getBadgeColor())
                    ->formatStateUsing(fn ($state) => $state->name()),

                TextColumn::make('description')
                    ->label('Опис')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birthday')
                    ->label(__('Дата народження'))
                    ->dateTime('d F Y р.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_seen_at')
                    ->label(__('Останній вхід'))
                    ->dateTime('d F Y р. о H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->label(__('Підтвердження email'))
                    ->dateTime('d F Y р. о H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('avatar')
                    ->label('Аватар')
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('backdrop')
                    ->label('Фон')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('allow_adult')
                    ->label('Дозволити дорослий контент')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_auto_next')
                    ->label('Автоматично наступне')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_auto_play')
                    ->label('Автовідтворення')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_auto_skip_intro')
                    ->label('Пропуск вступу')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_private_favorites')
                    ->label('Приватні улюблені')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            AchievementsUserRelationManager::class,
            WatchHistoriesRelationManager::class,
            CommentsRelationManager::class,
            SearchHistoriesRelationManager::class,
            SubscriptionsRelationManager::class,
            PaymentsRelationManager::class,
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
