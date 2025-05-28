<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms\Components\Section;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use AnimeSite\Enums\CommentReportType;
use AnimeSite\Filament\Resources\CommentReportResource\Pages;
use AnimeSite\Filament\Resources\CommentReportResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Comment;
use AnimeSite\Models\CommentReport;

class CommentReportResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';
    protected static ?string $model = CommentReport::class;
    protected static ?string $navigationGroup = 'Взаємодія';
    protected static ?string $pluralModelLabel = 'Скарги';
    protected static ?string $modelLabel = 'Скарга';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\Select::make('comment_id')
                            ->label('Коментар')
                            ->options(Comment::query()->pluck('body', 'id')) // Показує текст коментаря для вибору
                            ->searchable() // Додає поле пошуку
                            ->required()
                            ->reactive() // Дозволяє динамічно змінювати значення інших полів на основі вибору
                            ->afterStateUpdated(fn (callable $set, $state) => $set('user_id', Comment::find($state)?->user_id)),

                        Forms\Components\Select::make('user_id')
                            ->label('Користувач')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Деталі скарги')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Тип скарги')
                            ->required()
                            ->options(CommentReportType::labels())
                            ->enum(CommentReportType::class),

                        Forms\Components\TextInput::make('body')
                            ->label('Коментар до скарги')
                            ->nullable()
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Section::make('Статус')
                    ->schema([
                        Forms\Components\Checkbox::make('is_viewed')
                            ->label('Переглянуто?')
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('comment.body')
                    ->label('Коментар')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                TextColumn::make('type')
                    ->label('Тип скарги')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->sortable()
                    ->color(fn (CommentReportType $state): string => $state->getBadgeColor()),
                IconColumn::make('is_viewed')
                    ->label('Переглянуто')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('body')
                    ->label('Коментар до скарги')
                    ->limit(30),
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

        ])
            ->filters([

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommentReports::route('/'),
            'create' => Pages\CreateCommentReport::route('/create'),
            'edit' => Pages\EditCommentReport::route('/{record}/edit'),
        ];
    }
}
