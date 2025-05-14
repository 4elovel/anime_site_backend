<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RichEditor;
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
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Filament\Resources\PersonResource\Pages;
use AnimeSite\Filament\Resources\PersonResource\RelationManagers\AnimesRelationManager;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?string $pluralModelLabel = 'Люди';
    protected static ?string $modelLabel = 'Людина';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Name Section
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Ім\'я')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                                if ($operation == 'edit' || empty($state)) {
                                    return;
                                }
                                $set('slug', Person::generateSlug($state));
                                $set('meta_title', Person::makeMetaTitle($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),

                        TextInput::make('original_name')
                            ->label('Справжнє ім\'я')
                            ->maxLength(128)->columnSpan(2),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                // Description Section
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
                                $set('meta_description', Person::makeMetaDescription($plainText));
                            }),
                    ])
                    ->columnSpan(2),

                // Image Section
                Section::make()
                    ->schema([
                        FileUpload::make('image')
                            ->label('Зображення')
                            ->image()
                            ->directory('public/people')
                            ->maxSize(10240)
                            ->enableDownload()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (!empty($state)) {
                                    // Automatically set the meta_image to the same value as the uploaded image
                                    $set('meta_image', $state);
                                }
                            }),
                    ])
                    ->columnSpan(2),

                // Additional Information Section
                Section::make()
                    ->schema([
                        DatePicker::make('birthday')
                            ->label('Дата народження')
                            ->nullable(),

                        TextInput::make('birthplace')
                            ->label('Місце народження')
                            ->maxLength(248)
                            ->nullable(),

                        Select::make('type')
                            ->label('Тип')
                            ->options(PersonType::options())
                            ->required(),

                        Select::make('gender')
                            ->label('Стать')
                            ->options(Gender::labels())
                            ->nullable(),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                // SEO Settings Section
                Section::make(__('SEO Налаштування'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(128)
                            ->label(__('Meta заголовок')),

                        TextInput::make('meta_description')
                            ->maxLength(376)
                            ->label(__('Meta опис')),

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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Ім\'я')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('original_name')
                    ->label('Справжнє ім\'я')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->badge()
                    ->color(fn (PersonType $state): string => $state->getBadgeColor()),
                TextColumn::make('gender')
                    ->label('Стать')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->color(fn (Gender $state): string => $state->getBadgeColor()),

                TextColumn::make('slug')
                    ->label(('Slug'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->label('Зображення')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birthday')
                    ->label(__('Дата народження'))
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birthplace')
                    ->label('Місце народження')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('meta_title')
                    ->label(('Meta загаловок'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('meta_description')
                    ->label(__('Meta опис'))
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('meta_image')
                    ->label('Meta зображення')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->label('Gender')
                    ->options(Gender::labels())
                    ->multiple(),
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
            AnimesRelationManager::class,
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
