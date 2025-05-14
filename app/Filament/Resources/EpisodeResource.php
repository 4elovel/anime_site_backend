<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Group;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use AnimeSite\Enums\Country;
use AnimeSite\Enums\LanguageCode;
use AnimeSite\Enums\VideoQuality;
use AnimeSite\Filament\Resources\EpisodeResource\Pages;
use AnimeSite\Filament\Resources\EpisodeResource\RelationManagers\CommentsRelationManager;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Enums\VideoPlayerName;
use AnimeSite\ValueObjects\VideoPlayer;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $pluralModelLabel = 'Епізоди';
    protected static ?string $modelLabel = 'Епізод';

    public static function form(Form $form): Form
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
                        $set('slug', Episode::generateslug($state));
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

    public static function table(Tables\Table $table): Tables\Table
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
                Tables\Filters\SelectFilter::make('is_filler')
                    ->label('Is Filler')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
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
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }
}
