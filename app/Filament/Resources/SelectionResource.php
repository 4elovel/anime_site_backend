<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Filament\Resources\SelectionResource\Pages;
use Liamtseva\Cinema\Filament\Resources\SelectionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Models\Person;
use Liamtseva\Cinema\Models\Selection;
use Liamtseva\Cinema\Models\Tag;
use Liamtseva\Cinema\Models\User;

class SelectionResource extends Resource
{
    protected static ?string $model = Selection::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(), // Пошук по користувачам

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

                        MultiSelect::make('selectionable')
                            ->label('Пов’язаний об’єкт')
                            ->searchable()
                            ->required()
                            ->types([
                                Type::make(Episode::class)
                                    ->titleAttribute('name'),
                                Type::make(Anime::class)
                                    ->titleAttribute('name'),
                                Type::make(Tag::class)
                                    ->titleAttribute('name'),
                                Type::make(Person::class)
                                    ->titleAttribute('name'),
                            ])->columns(2),

                ])
                    ->columnSpan(2)
                    ->columns(2),

                Forms\Components\Section::make('Meta')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(128),

                        TextInput::make('meta_description')
                            ->maxLength(376),

                        TextInput::make('meta_image')
                            ->label('Meta image URL')
                            ->url()
                            ->maxLength(2048),
                    ])->columnSpan(1),


            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSelections::route('/'),
            'create' => Pages\CreateSelection::route('/create'),
            'edit' => Pages\EditSelection::route('/{record}/edit'),
        ];
    }
}
