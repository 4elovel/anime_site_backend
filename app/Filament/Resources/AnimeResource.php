<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Liamtseva\Cinema\Enums\ApiSourceName;
use Liamtseva\Cinema\Enums\AttachmentType;
use Liamtseva\Cinema\Enums\Country;
use Liamtseva\Cinema\Enums\Kind;
use Liamtseva\Cinema\Enums\Period;
use Liamtseva\Cinema\Enums\RelatedType;
use Liamtseva\Cinema\Enums\RestrictedRating;
use Liamtseva\Cinema\Enums\Source;
use Liamtseva\Cinema\Enums\Status;
use Liamtseva\Cinema\Filament\Resources\AnimeResource\Pages;
use Liamtseva\Cinema\Filament\Resources\AnimeResource\RelationManagers\TagsRelationManager;
use Liamtseva\Cinema\Models\Anime;


use Liamtseva\Cinema\Models\Studio;
use Liamtseva\Cinema\ValueObjects\Attachment;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class AnimeResource extends Resource
{
    protected static ?string $model = Anime::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        TagsInput::make('aliases')->required()
                            ])
                            ->columns(2),


                        Repeater::make('api_sources')
                            ->label('API Sources')
                            ->schema([
                                Select::make('source')
                                    ->label('Source')
                                    ->options(function () {

                                        return ApiSourceName::labels();
                                    })->default(ApiSourceName::TMDB->value)
                                    ->required(),

                                TextInput::make('id')
                                    ->label('ID')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->required(),

                    ])->columnSpan(2),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(5)  // Кількість рядків
                    ->maxLength(500)  // Максимальна кількість символів
                    ->placeholder('Enter your description here...')
                    ->required(),

                Select::make('studio_id')  // Поле для вибору студії
                ->label('Studio')
                    ->options(Studio::all()->pluck('name', 'id'))  // Отримуємо список студій (назва => id)
                    ->required(),  // Робимо поле обов'язковим

                        FileUpload::make('image_name')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('images')
                            ->helperText('Upload an image file')
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    return $state;
                                }
                            }),


                        FileUpload::make('poster')
                            ->label('Poster')
                            ->image()
                            ->disk('public')
                            ->directory('posters')
                            ->helperText('Upload an image file')
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    return $state;
                                }
                            }),

                Repeater::make('attachments')
                    ->label('Attachments')
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(AttachmentType::labels())
                            ->default(AttachmentType::PICTURE->value)
                            ->required(),

                        TextInput::make('src')
                            ->label('Source URL')
                            ->url()  // Перевірка, що це URL
                            ->required(),
                    ])
                    ->columns(2)
                    ->required()
                    ->columnSpan(2),

                Select::make('kind')
                    ->label('Kind')
                    ->options(Kind::options())
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options(Status::options())
                    ->required(),

                Select::make('period')
                    ->label('Period')
                    ->options(Period::options())
                    ->nullable(),

                Select::make('restricted_rating')
                    ->label('Restricted Rating')
                    ->options(RestrictedRating::options())
                    ->nullable(),

                Select::make('source')
                    ->label('Source')
                    ->options(Source::options())
                    ->nullable(),


                TextInput::make('duration')
                    ->label('Duration')
                    ->numeric()
                    ->nullable(),

                TextInput::make('episodes_count')
                    ->label('Episodes Count')
                    ->numeric()
                    ->nullable(),

                DatePicker::make('first_air_date')
                    ->label('First Air Date')
                    ->nullable(),

                DatePicker::make('last_air_date')
                    ->label('Last Air Date')
                    ->nullable(),


                Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),

                Repeater::make('countries')  // Поле для країн
                ->label('Countries')
                    ->schema([
                        Select::make('countries')  // Поле для вибору країни
                        ->label('Countries')
                            ->options(Country::toArray())   // Використовуємо метод enum для отримання списку країн
                            ->required()
                            ->default(fn () => [Country::JAPAN->value]),
                    ])
                    ->columns(1)
                    ->columnSpan(2)
                    ->dehydrateStateUsing(fn ($state) => collect($state)->map(fn ($item) => $item['countries'])->toArray()),


                Repeater::make('relateds')
                    ->label('Related Anime')
                    ->schema([
                        Select::make('anime_id')
                            ->label('Anime ID')
                            ->options(Anime::all()->pluck('name', 'id')) // Вибірка всіх аніме з таблиці
                            ->required(),

                        Select::make('type')
                            ->label('Type')
                            ->options(RelatedType::labels()) // Отримуємо варіанти типів через метод labels()
                            ->default(RelatedType::SEASON->value)  // За замовчуванням вибираємо 'season'
                            ->required(),  // Обов'язкове поле для типу
                    ])
                    ->columns(2)  // Розподіл на два стовпці для зручності
                    ->required()
                    ->columnSpan(2),

                Repeater::make('similars')
                    ->label('Similars')
                    ->schema([
                        Select::make('anime_id')
                            ->label('Anime ID')
                            ->options(function ($get) {
                                $currentAnimeId = $get('id'); // Отримуємо поточний ID аніме, щоб не додавати себе
                                return Anime::where('id', '!=', $currentAnimeId)  // Вибірка аніме, які не є поточним
                                ->pluck('name', 'id');  // Вибір ID та назви
                            })
                            ->required(),  // Обов'язкове поле
                    ])
                    ->columns(1)  // Один стовпець для кожного елемента
                    ->defaultItems(1)  // За замовчуванням можна додати 1 запис
                    ->required()
                    ->columnSpan(2),

                Repeater::make('scores')
                    ->label('Scores')
                    ->schema([
                        Select::make('source')
                            ->label('Source')
                            ->options(function () {
                                // Повертаємо можливі варіанти джерел з Enum ApiSourceName
                                return ApiSourceName::labels();
                            })
                            ->default(ApiSourceName::IMDB->value) // Встановлюємо за замовчуванням IMDB
                            ->required(),

                        TextInput::make('value')
                            ->label('Score Value')
                            ->numeric()  // Оскільки значення буде числовим
                            ->required(),
                    ])
                    ->columns(2)  // Розподіл в два стовпці
                    ->required()
                    ->columnSpan(2),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->sortable(),


                TextColumn::make('kind')
                    ->formatStateUsing(fn ($state) => $state->name()) // Використовуємо метод name() для відображення

                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->colors([
                        'success' => ['released', 'ongoing'],
                        'danger' => ['canceled', 'rumored'],
                        'info' => ['anons'],
                    ]),

                TextColumn::make('name')
                    ->label('Anime Name')
                    ->sortable(),

                BooleanColumn::make('is_published')
                    ->label('Published')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->label('Published')
                    ->query(fn ($query) => $query->where('is_published', true)),

                SelectFilter::make('kind')
                    ->label('Kind')
                    ->options(Kind::options()) // Тепер це працює з людськими назвами та правильними значеннями
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
            TagsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnimes::route('/'),
            'create' => Pages\CreateAnime::route('/create'),
            'edit' => Pages\EditAnime::route('/{record}/edit'),
        ];
    }
}
