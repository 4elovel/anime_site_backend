<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liamtseva\Cinema\Enums\CommentReportType;
use Liamtseva\Cinema\Filament\Resources\CommentReportResource\Pages;
use Liamtseva\Cinema\Filament\Resources\CommentReportResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Liamtseva\Cinema\Models\Comment;
use Liamtseva\Cinema\Models\CommentReport;

class CommentReportResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';
    protected static ?string $model = CommentReport::class;
    protected static ?string $navigationSubGroup  = 'Коментарі';
    protected static ?string $navigationGroup = 'Взаємодія';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('comment_id')
                    ->label('Коментар')
                    ->options(Comment::query()->pluck('body', 'id')) // Показує текст коментаря для вибору
                    ->searchable() // Додає поле пошуку
                    ->required()
                    ->reactive() // Дозволяє динамічно змінювати значення інших полів на основі вибору
                    ->afterStateUpdated(fn (callable $set, $state) => $set('user_id', Comment::find($state)?->user_id)),

                Forms\Components\TextInput::make('user_id')
                    ->label('Користувач')

                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Тип скарги')
                    ->required()
                    ->options(CommentReportType::labels()) // Використовуємо метод labels()
                    ->enum(CommentReportType::class), // Прив'язуємо Enum для валідації

                Forms\Components\TextInput::make('body')
                    ->label('Коментар до скарги')
                    ->required()
                    ->nullable() // Поле може бути порожнім
                    ->maxLength(255),

                Forms\Components\Checkbox::make('is_viewed')
                    ->label('Чи переглянуто?')
                    ->default(false), // Значення за замовчуванням false
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment.body')
                    ->label('Коментар')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Str::limit($state, 40, '...')),
                TextColumn::make('type')
                    ->label('Тип скарги')
                    ->formatStateUsing(fn ($state) => $state->name()) // Використовуйте $state
                    ->sortable()
                    ->color(fn ($state) => match ($state) {
                        CommentReportType::INSULT => 'primary',
                        CommentReportType::FLOOD_OFFTOP_MEANINGLESS => 'secondary',
                        CommentReportType::AD_SPAM => 'warning',
                        CommentReportType::SPOILER => 'info',
                        CommentReportType::PROVOCATION_CONFLICT => 'danger',
                        CommentReportType::INAPPROPRIATE_LANGUAGE => 'success',
                        default => 'muted',
                    }),
                Tables\Columns\BooleanColumn::make('is_viewed')
                    ->label('Переглянуто')
                    ->trueIcon('heroicon-o-check-circle')  // Іконка для значення true
                    ->falseIcon('heroicon-o-x-circle')    // Іконка для значення false
                    ->trueColor('success')                // Колір для значення true
                    ->falseColor('danger')                // Колір для значення false
                    ->sortable(),

        ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип скарги')
                    ->options(CommentReportType::labels()),
            ])
        ->actions([
        EditAction::make()
            ->label('Edit')
            ->icon('heroicon-o-pencil')
            ->color('primary'),

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommentReports::route('/'),
            'create' => Pages\CreateCommentReport::route('/create'),
            'edit' => Pages\EditCommentReport::route('/{record}/edit'),
        ];
    }
}
