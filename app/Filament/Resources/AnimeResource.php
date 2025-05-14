<?php

namespace AnimeSite\Filament\Resources;

use AnimeSite\Services\TmdbService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use AnimeSite\Enums\ApiSourceName;
use AnimeSite\Enums\AttachmentType;
use AnimeSite\Enums\Country;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RelatedType;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;
use AnimeSite\Filament\Resources\AnimeResource\Pages;
use AnimeSite\Filament\Resources\AnimeResource\RelationManagers\CommentsRelationManager;
use AnimeSite\Filament\Resources\AnimeResource\RelationManagers\EpisodesRelationManager;
use AnimeSite\Filament\Resources\AnimeResource\RelationManagers\PeoplePivotRelationManager;
use AnimeSite\Filament\Resources\AnimeResource\RelationManagers\PeopleRelationManager;
use AnimeSite\Filament\Resources\AnimeResource\RelationManagers\TagsRelationManager;
use AnimeSite\Models\Anime;


use AnimeSite\Models\Studio;
use AnimeSite\ValueObjects\Attachment;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class AnimeResource extends Resource
{
    protected static ?string $model = Anime::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $modelLabel = 'Аніме';
    protected static ?string $pluralModelLabel = 'Аніме';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Назва')
                                    ->required()
                                    ->maxLength(255)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Anime::generateslug($state));
                                        $set('meta_title', $state);
                                    }),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(128)
                                    ->unique(ignoreRecord: true),

                                TagsInput::make('aliases')
                                    ->label('Псевдоніми')
                                    ->required()->columnSpan(2),
                            ])
                            ->columns(2),


                        Repeater::make('api_sources')
                            ->label('API джерело')
                            ->schema([
                                Select::make('source')
                                    ->label('Джерело')
                                    ->options(function () {

                                        return ApiSourceName::labels();
                                    })->default(ApiSourceName::TMDB->value)
                                    ->required(),


                                TextInput::make('id')
                                    ->label('ID')
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                        if ($get('source') == ApiSourceName::TMDB->value) {
                                            if (!$state) return;

                                            $tmdb = new TmdbService();
                                            $data = $tmdb->getAnimeById($state);

                                            if (!$data) {
                                                $livewire->addError('api_sources', 'Failed to fetch data from TMDB');
                                                return;
                                            }

                                            $formattedData = $tmdb->formatDataForForm($data);
                                            $livewire->form->fill($formattedData);
                                        }
                                    })
                                    ->reactive()
                                    ->debounce(600)
                                ,
                            ])
                            ->columns(2)
                            ->required(),

                    ])->columnSpan(2),
                Section::make()
                    ->schema([
                        RichEditor::make('description')
                            ->label('Опис')
                            ->maxLength(512)
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3', 'h4', 'bulletList', 'orderedList',
                                'link', 'blockquote', 'codeBlock', 'undo', 'redo',
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                                if ($operation == 'edit' || empty($state)) {
                                    return;
                                }
                                $plainText = strip_tags($state);
                                $set('meta_description', Anime::makeMetaDescription($plainText));
                            }),


                    ]),

                Section::make()
                    ->schema([
                        Select::make('studio_id')  // Поле для вибору студії
                        ->label('Студія')
                            ->options(Studio::all()->pluck('name', 'id'))  // Отримуємо список студій (назва => id)
                            ->required(),  // Робимо поле обов'язковим
                        Select::make('kind')
                            ->label('Жанр')
                            ->options(Kind::options())
                            ->required(),

                        Select::make('status')
                            ->label('Статус')
                            ->options(Status::options())
                            ->required(),

                        Select::make('period')
                            ->label('Період')
                            ->options(Period::options())
                            ->nullable(),

                        Select::make('restricted_rating')
                            ->label('Вікове обмеження')
                            ->options(RestrictedRating::options())
                            ->nullable(),

                        Select::make('source')
                            ->label('Джерело')
                            ->options(Source::options())
                            ->nullable(),


                        TextInput::make('duration')
                            ->label('Тривалість')
                            ->numeric()
                            ->nullable(),

                        TextInput::make('episodes_count')
                            ->label('Кількість епізодів')
                            ->numeric()
                            ->nullable(),

                        DatePicker::make('first_air_date')
                            ->label('Перша дата виходу')
                            ->nullable(),

                        DatePicker::make('last_air_date')
                            ->label('Остання дата виходу')
                            ->nullable(),


                        Toggle::make('is_published')
                            ->label('Опубліковано')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make()
                    ->schema([
                FileUpload::make('image_name')
                    ->label('Зображення')
                    ->image()
                    ->disk('public')
                    ->directory('images')
                    ->helperText('Upload an image file')
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (!empty($state)) {
                            $set('meta_image', $state);
                        }
                    }),
                FileUpload::make('poster')
                    ->label('Постер')
                    ->image()
                    ->disk('public')
                    ->directory('posters')
                    ->helperText('Upload an image file')
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            return $state;
                        }
                    }),
            ])
            ->columns(2),

                Repeater::make('attachments')
                    ->label('Медіа')
                    ->schema([
                        Select::make('type')
                            ->label('Тип')
                            ->options(AttachmentType::labels())
                            ->default(AttachmentType::PICTURE->value)
                            ->required(),

                        TextInput::make('src')
                            ->label('URL джерела')
                            ->url()  // Перевірка, що це URL
                            ->required(),
                    ])
                    ->columns(2)
                    ->required()
                    ->columnSpan(2),

                Repeater::make('countries')  // Поле для країн
                ->label('Країни')
                    ->schema([
                        Select::make('countries')  // Поле для вибору країни
                        ->label('Країна')
                            ->options(Country::toArray())   // Використовуємо метод enum для отримання списку країн
                            ->required()
                            ->default(fn () => [Country::JA_JP->value]),
                    ])
                    ->columns(1)
                    ->columnSpan(2)
                    ->dehydrateStateUsing(fn ($state) => collect($state)->map(fn ($item) => $item['countries'])->toArray()),


                Repeater::make('relateds')
                    ->label('Пов\'язані аніме')
                    ->schema([
                        Select::make('anime_id')
                            ->label('Аніме ID')
                            ->options(Anime::all()->pluck('name', 'id')) // Вибірка всіх аніме з таблиці
                            ->required(),

                        Select::make('type')
                            ->label('Тип')
                            ->options(RelatedType::labels()) // Отримуємо варіанти типів через метод labels()
                            ->default(RelatedType::SEASON->value)  // За замовчуванням вибираємо 'season'
                            ->required(),  // Обов'язкове поле для типу
                    ])
                    ->columns(2)  // Розподіл на два стовпці для зручності
                    ->required()
                    ->columnSpan(2),

                Repeater::make('similars')
                    ->label('Схожі анме')
                    ->schema([
                        Select::make('anime_id')
                            ->label('Аніме ID')
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
                    ->label('Рейтинг')
                    ->schema([
                        Select::make('source')
                            ->label('ДЖерело')
                            ->options(function () {
                                // Повертаємо можливі варіанти джерел з Enum ApiSourceName
                                return ApiSourceName::labels();
                            })
                            ->default(ApiSourceName::IMDB->value) // Встановлюємо за замовчуванням IMDB
                            ->required(),

                        TextInput::make('value')
                            ->label('Рейтинг')
                            ->numeric()  // Оскільки значення буде числовим
                            ->required(),
                    ])
                    ->columns(2)  // Розподіл в два стовпці
                    ->required()
                    ->columnSpan(2),

                Section::make(__('SEO Налаштування'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // Meta Title Input
                        TextInput::make('meta_title')
                            ->maxLength(128)
                            ->label(__('Meta заголовок')),

                        // Meta Description Input
                        TextInput::make('meta_description')
                            ->maxLength(376)
                            ->label(__('Meta опис')),

                        // Meta Image Upload
                        FileUpload::make('meta_image')
                            ->image()
                            ->directory('public/meta')
                            ->label(__('Meta зображення')),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Назва')
                    ->sortable(),

                TextColumn::make('kind')
                    ->label('Жанр')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->badge()
                    ->color(fn (Kind $state): string => $state->getBadgeColor())
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->badge()
                    ->color(fn (Status $state): string => $state->getBadgeColor()),

                BooleanColumn::make('is_published')
                    ->label('Опубліковано')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('aliases')
                    ->label('Псевдоніми')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('studio.name')
                    ->label('Студія')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('duration')
                    ->label('Тривалість')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('episodes_count')
                    ->label('Кількість епізодів')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('imdb_score')
                    ->label('Оцінка IMDB')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('first_air_date')
                    ->label(__('Дата початку ефіру'))
                    ->dateTime('d F Y р.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_air_date')
                    ->label(__('Дата завершення ефіру'))
                    ->dateTime('d F Y р.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('api_sources')
                    ->label('API Джерела')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('attachments')
                    ->label('Медіа')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('related')
                    ->label('Пов’язані фільми')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('similars')
                    ->label('Схожі фільми')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('period')
                    ->label('Період')
                    ->formatStateUsing(fn ($state) => $state?->name())
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('restricted_rating')
                    ->label('Віковий рейтинг')
                    ->formatStateUsing(fn ($state) => $state?->name())
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source')
                    ->label('Джерело')
                    ->formatStateUsing(fn ($state) => $state?->name())
                    ->badge()
                    ->color(fn (Source $state): string => $state->getBadgeColor())
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('poster')
                    ->label('Постер')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_title')
                    ->label(('Meta заголовок'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_description')
                    ->label(__('Meta опис'))
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('meta_image')
                    ->label('Meta зображення')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->label('Published')
                    ->query(fn ($query) => $query->where('is_published', true)),
                SelectFilter::make('studio')
                    ->label('Студія')
                    ->relationship('studio', 'name')
                    ->searchable(),

            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc'); // Default sort by creation date, descending

    }

    public static function getRelations(): array
    {
        return [
            TagsRelationManager::class,
            PeoplePivotRelationManager::class,
            EpisodesRelationManager::class,
            CommentsRelationManager::class,
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
