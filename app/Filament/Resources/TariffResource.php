<?php

namespace AnimeSite\Filament\Resources;

use AnimeSite\Enums\Currency;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use AnimeSite\Enums\TariffFeature;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use AnimeSite\Filament\Resources\TariffResource\Pages;
use AnimeSite\Filament\Resources\TariffResource\RelationManagers\PaymentsRelationManager;
use AnimeSite\Filament\Resources\TariffResource\RelationManagers\SubscriptionsRelationManager;
use AnimeSite\Models\Tariff;

class TariffResource extends Resource
{
    protected static ?string $model = Tariff::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Підписки';
    protected static ?string $pluralModelLabel = 'Тарифи';
    protected static ?string $modelLabel = 'Тариф';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Section
                Section::make()
                    ->schema([
                        // Name Input
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (empty($state)) return;
                                $set('slug', Tariff::generateSlug($state));
                                $set('meta_title', Tariff::makeMetaTitle($state));
                            }),

                        // Slug Input
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                // Pricing Section
                Section::make()
                    ->schema([
                        TextInput::make('price')
                            ->label('Ціна')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),

                        Select::make('currency')
                            ->label('Валюта')
                            ->required()
                            ->options(Currency::labels())
                            ->default(Currency::UAH->value),

                        TextInput::make('duration_days')
                            ->label('Тривалість (днів)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(30),

                        Toggle::make('is_active')
                            ->label('Активний')
                            ->default(true),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                // Description Section
                Section::make()
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Опис')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3', 'h4', 'bulletList', 'orderedList',
                                'link', 'blockquote', 'undo', 'redo',
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                                if ($operation == 'edit' || empty($state)) {
                                    return;
                                }
                                $plainText = strip_tags($state);
                                $set('meta_description', Tariff::makeMetaDescription($plainText));
                            })
                            ->columnSpan(2),

                        CheckboxList::make('features')
                            ->label('Функції')
                            ->options(TariffFeature::options())
                            ->descriptions(collect(TariffFeature::cases())->mapWithKeys(fn ($feature) => [$feature->value => $feature->description()])->toArray())
                            ->required()
                            ->columns(2)
                            ->columnSpan(2),
                    ])
                    ->columnSpan(2),

                // SEO Settings Section
                Section::make('SEO Налаштування')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // Meta Title
                        TextInput::make('meta_title')
                            ->maxLength(128)
                            ->label('Meta заголовок'),

                        // Meta Description
                        TextInput::make('meta_description')
                            ->maxLength(376)
                            ->label('Meta опис'),

                        // Meta Image
                        FileUpload::make('meta_image')
                            ->image()
                            ->directory('public/meta')
                            ->label('Meta зображення'),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(2);
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
                    ->label('Назва')
                    ->sortable()
                    ->searchable()
                ->limit(20),

                TextColumn::make('price')
                    ->label('Ціна')
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Валюта')
                    ->sortable(),

                TextColumn::make('duration_days')
                    ->label('Тривалість (днів)')
                    ->sortable(),

                TextColumn::make('features')
                    ->label('Функції')
                    ->formatStateUsing(function ($state) {
                        // Якщо значення це рядок, розділяємо його по комі
                        $features = is_string($state) ? explode(', ', $state) : $state;

                        // Перетворюємо в нормальний вигляд
                        return collect($features)
                            ->map(function (string $featureValue) {
                                $feature = TariffFeature::tryFrom($featureValue);
                                return $feature ? $feature->name() : $featureValue;
                            })
                            ->implode(', ');
                    })
                    ->searchable()
                ->wrap(),

                BooleanColumn::make('is_active')
                    ->label('Активний')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Активні')
                    ->query(fn ($query) => $query->where('is_active', true)),
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
            SubscriptionsRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTariffs::route('/'),
            'create' => Pages\CreateTariff::route('/create'),
            'edit' => Pages\EditTariff::route('/{record}/edit'),
        ];
    }
}
