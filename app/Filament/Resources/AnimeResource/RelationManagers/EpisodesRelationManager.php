<?php

namespace AnimeSite\Filament\Resources\AnimeResource\RelationManagers;

use AnimeSite\Enums\LanguageCode;
use AnimeSite\Enums\VideoPlayerName;
use AnimeSite\Enums\VideoQuality;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'episodes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Episode::generateSlug($state));
                                $set('meta_title', $state);
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),

                    ])->columns(2),

                Section::make(__(''))
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
                                $set('meta_description', Episode::makeMetaDescription($plainText));
                            }),
                    ]),

                Section::make(__(''))
                    ->schema([

                        Select::make('anime_id')
                            ->label('Аніме')
                            ->options(Anime::all()->pluck('name', 'id'))
                            ->required()
                            ->columnSpan(2),

                        DatePicker::make('air_date')
                            ->label('Дата виходу')
                            ->nullable()
                            ->columnSpan(1),

                        TextInput::make('duration')
                            ->label('Тривалість')
                            ->numeric()
                            ->required(),

                        TextInput::make('number')
                            ->label('Номер')
                            ->numeric()
                            ->required(),


                        Toggle::make('is_filler')
                            ->label('Філлер')
                            ->default(false),

                        TagsInput::make('pictures')
                            ->required()
                            ->label(__('Зображення'))
                            ->columnSpan(3),

                    ])
                    ->columns(3),
                Section::make(__(''))
                    ->schema([
                        Repeater::make('video_players')
                            ->label('Відео плеєри')
                            ->schema([
                                Select::make('name')
                                    ->label('Назва')
                                    ->options(function () {
                                        return VideoPlayerName::labels();
                                    })->default(VideoPlayerName::KODIK->value)
                                    ->required(),

                                TextInput::make('url')
                                    ->label('Url')
                                    ->url()
                                    ->default('')
                                    ->required(),

                                TextInput::make('file_url')
                                    ->label('Url файлу')
                                    ->url()
                                    ->default('')
                                    ->required(),

                                Select::make('dubbing')
                                    ->label('Дубляж')
                                    ->options(LanguageCode::options())
                                    ->default(LanguageCode::JAPANESE->value)
                                    ->required(),

                                Select::make('quality')
                                    ->label('Якість')
                                    ->options(function () {
                                        return VideoQuality::options();
                                    })
                                    ->default(VideoQuality::FULL_HD->value)
                                    ->required(),

                                Select::make('locale_code')
                                    ->label('Локальний код')
                                    ->options(LanguageCode::options())
                                    ->default(LanguageCode::JAPANESE->value)
                                    ->required(),
                            ])
                            ->required()
                            ->afterStateUpdated(function ($state, $set) {
                                foreach ($state as $key => $value) {
                                    if (empty($value)) {
                                        $state[$key] = 'N/A';
                                    }
                                }
                                $set('video_players', $state);
                            })->columns(2),

                    ]),


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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('anime.name')
                    ->label('Аніме')
                    ->sortable(),

                TextColumn::make('number')
                    ->label('Номер')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Назва'),

                TextColumn::make('description')
                    ->label('Опис')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->toggleable(),

                TextColumn::make('duration')
                    ->label('Тривалість (хв)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('air_date')
                    ->label('Дата виходу')
                    ->sortable()
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),

                BooleanColumn::make('is_filler')
                    ->label('Філер')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pictures')
                    ->label('Зображення')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('meta_title')
                    ->label('Meta заголовок')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_description')
                    ->label('Meta опис')
                    ->limit(100)
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('meta_image')
                    ->label('Meta зображення')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->sortable()
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->sortable()
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('aired')
                    ->label('Вийшли в ефір')
                    ->query(fn (Builder $query) => $query->whereNotNull('air_date')->where('air_date', '<=', now())),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Очікуються')
                    ->query(fn (Builder $query) => $query->where('air_date', '>', now())),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати епізод')
                    ->form([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Str::slug($state));
                                $set('meta_title', $state);
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),

                    ])->columns(2),

                Section::make(__(''))
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
                                $set('meta_description', Episode::makeMetaDescription($plainText));
                            }),
                    ]),

                Section::make(__(''))
                    ->schema([

                        Select::make('anime_id')
                            ->label('Аніме')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->options(Anime::query()->pluck('name', 'id'))
                            ->dehydrated()
                            ->required()
                            ->columnSpan(2),

                        DatePicker::make('air_date')
                            ->label('Дата виходу')
                            ->nullable()
                            ->columnSpan(1),

                        TextInput::make('duration')
                            ->label('Тривалість')
                            ->numeric()
                            ->required(),

                        TextInput::make('number')
                            ->label('Номер')
                            ->numeric()
                            ->required(),


                        Toggle::make('is_filler')
                            ->label('Філлер')
                            ->default(false),

                        TagsInput::make('pictures')
                            ->required()
                            ->label(__('Зображення'))
                            ->columnSpan(3),

                    ])
                    ->columns(3),
                Section::make(__(''))
                    ->schema([
                        Repeater::make('video_players')
                            ->label('Відео плеєри')
                            ->schema([
                                Select::make('name')
                                    ->label('Назва')
                                    ->options(function () {
                                        return VideoPlayerName::labels();
                                    })->default(VideoPlayerName::KODIK->value)
                                    ->required(),

                                TextInput::make('url')
                                    ->label('Url')
                                    ->url()
                                    ->default('')
                                    ->required(),

                                TextInput::make('file_url')
                                    ->label('Url файлу')
                                    ->url()
                                    ->default('')
                                    ->required(),

                                Select::make('dubbing')
                                    ->label('Дубляж')
                                    ->options(LanguageCode::options())
                                    ->default(LanguageCode::JAPANESE->value)
                                    ->required(),

                                Select::make('quality')
                                    ->label('Якість')
                                    ->options(function () {
                                        return VideoQuality::options();
                                    })
                                    ->default(VideoQuality::FULL_HD->value)
                                    ->required(),

                                Select::make('locale_code')
                                    ->label('Локальний код')
                                    ->options(LanguageCode::options())
                                    ->default(LanguageCode::JAPANESE->value)
                                    ->required(),
                            ])
                            ->required()
                            ->afterStateUpdated(function ($state, $set) {
                                foreach ($state as $key => $value) {
                                    if (empty($value)) {
                                        $state[$key] = 'N/A';
                                    }
                                }
                                $set('video_players', $state);
                            })->columns(2),

                    ]),


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
                ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('episodes')->insert([
                            'anime_id' => $data['anime_id'],
                            'number' => $data['number'],
                            'slug' => $data['slug'],
                            'name' => $data['name'],
                            'description' => $data['description'] ?? '',
                            'duration' => $data['duration'] ?? null,
                            'air_date' => $data['air_date'] ?? null,
                            'is_filler' => $data['is_filler'] ?? false,
                            'pictures' => $data['pictures'] ?? [],
                            'video_players' => $data['video_players'] ?? [],
                            'meta_title' => $data['meta_title'] ?? null,
                            'meta_description' => $data['meta_description'] ?? null,
                            'meta_image' => $data['meta_image'] ?? null,
                        ]);

                        return Episode::find($data['person_id']);
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
            ->defaultSort('number');
    }
}
