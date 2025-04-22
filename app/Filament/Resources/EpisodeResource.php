<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Enums\Country;
use Liamtseva\Cinema\Enums\LanguageCode;
use Liamtseva\Cinema\Enums\VideoQuality;
use Liamtseva\Cinema\Filament\Resources\EpisodeResource\Pages;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Enums\VideoPlayerName;
use Liamtseva\Cinema\ValueObjects\VideoPlayer;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
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

                TextInput::make('description')
                    ->required()
                    ->maxLength(512),

                Select::make('anime_id')
                ->label('Anime')
                    ->options(Anime::all()->pluck('name', 'id'))
                    ->required(),

                TextInput::make('duration')
                    ->label('Duration')
                    ->numeric()
                    ->required(),

                DatePicker::make('air_date')
                    ->label('Air Date')
                    ->nullable(),

                Toggle::make('is_filler')
                    ->label('Is filler')
                    ->default(false),

                TagsInput::make('pictures')
                    ->label('Pictures')
                    ->required()
                    ->helperText('Введіть URL-адреси для кожного зображення.'),

                Repeater::make('video_players')
                    ->label('Video players')
                    ->schema([
                        Select::make('name')
                            ->label('Name')
                            ->options(function () {
                                return VideoPlayerName::labels();
                            })->default(VideoPlayerName::KODIK->value)
                            ->required(),

                        TextInput::make('url')
                            ->label('Url')
                            ->url()
                            ->required(),

                        TextInput::make('file_url')
                            ->label('File url')
                            ->url()
                            ->required(),

                        Select::make('dubbing')
                            ->label('Dubbing')
                            ->options(function () {
                                return LanguageCode::all();
                            })->default(LanguageCode::JAPANESE->value)
                            ->required(),

                        Select::make('quality')
                            ->label('Quality')
                            ->options(function () {
                                return VideoQuality::options();
                            })->default(VideoQuality::FULL_HD->value)
                            ->required(),

                        Select::make('locale_code')
                            ->label('Locale code')
                            ->options(function () {
                                return LanguageCode::options();
                            })->default(LanguageCode::JAPANESE->value)
                            ->required(),
                    ])
                    ->columns(2)
                    ->required(),


                TextInput::make('meta_title')
                    ->maxLength(128),

                TextInput::make('meta_description')
                    ->maxLength(376),

                TextInput::make('meta_image')
                    ->label('Meta image URL')
                    ->url()
                    ->maxLength(2048),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Episode Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('air_date')
                    ->label('Air Date')
                    ->date(),
                Tables\Columns\BooleanColumn::make('is_filler')
                    ->label('Is Filler'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_filler')
                    ->label('Is Filler')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
            ]);
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
