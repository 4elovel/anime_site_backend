<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Enums\Gender;
use Liamtseva\Cinema\Enums\PersonType;
use Liamtseva\Cinema\Filament\Resources\PersonResource\Pages;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Person;
use Liamtseva\Cinema\Models\Studio;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(128)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(128)
                    ->unique(ignoreRecord: true),

                TextInput::make('original_name')
                    ->label('Original name')
                    ->maxLength(128),


                TextInput::make('description')
                    ->maxLength(512),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                        Section::make('Зображення')
                            ->schema([
                                FileUpload::make('image') // Поле для завантаження файлів
                                ->label('Завантажити файл') // Підпис для поля
                                ->image() // Якщо ви хочете, щоб завантажувались тільки зображення
                                ->disk('public') // Диск для збереження файлів (визначається у config/filesystems.php)
                                ->directory('uploads') // Каталог для збереження файлів
                                ->maxSize(10240) // Максимальний розмір файлу в КБ (наприклад, 10 МБ)
                                ->enableDownload(),
                            ])->columnSpan(1),


                        Group::make()
                            ->schema([
                                DatePicker::make('birthday')
                                    ->label('Birthday')
                                    ->nullable(),

                                TextInput::make('birthplace')
                                    ->maxLength(248)
                                    ->nullable(),

                                Select::make('type')
                                    ->label('Type')
                                    ->options(PersonType::options())
                                    ->required(),


                                Select::make('gender')
                                    ->label('Gender')
                                    ->options(Gender::labels())
                                    ->nullable(),
                            ])
                            ->columnSpan(2)
                            ->columns(2),


                Section::make('Meta')
                    ->schema([
                TextInput::make('meta_title')
                    ->maxLength(128),

                TextInput::make('meta_description')
                    ->maxLength(376),

                TextInput::make('meta_image')
                    ->label('Meta image URL')
                    ->url()
                    ->columnSpan(2),
                    ])
                    ->columnSpan(1)
                    ->columns(1),
            ])
            ->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('original_name')
                    ->label('Original Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state->name()) // Форматуємо для виведення імені
                    ->color(fn ($state) => match ($state) {
                        PersonType::CHARACTER => 'primary',           // Синій
                        PersonType::DIRECTOR => 'success',            // Зелений
                        PersonType::PRODUCER => 'info',               // Світло-блакитний
                        PersonType::WRITER => 'warning',              // Жовтий
                        PersonType::EDITOR => 'danger',               // Червоний
                        PersonType::CINEMATOGRAPHER => 'muted',       // Сірий
                        PersonType::COMPOSER => 'primary',            // Синій
                        PersonType::ART_DIRECTOR => 'success',        // Зелений
                        PersonType::SOUND_DESIGNER => 'info',         // Світло-блакитний
                        PersonType::MAKEUP_ARTIST => 'secondary',     // Темно-сірий
                        PersonType::VOICE_ACTOR => 'primary',         // Синій
                        PersonType::STUNT_PERFORMER => 'warning',     // Жовтий
                        PersonType::ASSISTANT_DIRECTOR => 'danger',   // Червоний
                        PersonType::PRODUCER_ASSISTANT => 'muted',    // Сірий
                        PersonType::SCRIPT_SUPERVISOR => 'info',      // Світло-блакитний
                        PersonType::PRODUCTION_DESIGNER => 'success', // Зелений
                        PersonType::VISUAL_EFFECTS_SUPERVISOR => 'warning', // Жовтий
                        default => 'muted',                           // За замовчуванням — сірий
                    }),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn ($state) => $state->name()) // Форматуємо стан, щоб відобразити ім'я
                    ->color(fn ($state) => match ($state) {
                        Gender::MALE => 'primary',
                        Gender::OTHER => 'info',
                        Gender::FEMALE => 'danger',
                        default => 'muted',
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(PersonType::options())
                    ->multiple(),
                SelectFilter::make('gender')
                    ->label('Gender')
                    ->options(Gender::labels())
                    ->multiple(),
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
                    ->url(fn (Model $record) => route('anime.show', $record)),

                // Додавання дії "Видалити"
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->modalHeading('Are you sure you want to delete this record?')
                    ->modalSubheading('This action cannot be undone.')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
