<?php

namespace AnimeSite\Filament\Resources\CommentResource\RelationManagers;

use AnimeSite\Models\Comment;
use AnimeSite\Models\CommentReport;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\CommentReportType;
use AnimeSite\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип скарги')
                    ->options(collect(CommentReportType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->name()]))
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати скаргу')
                    ->form([
                        Select::make('comment_id')
                            ->label('Коментар')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(Comment::query()->pluck('body', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Користувач')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
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
                            ])
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        return CommentReport::create([
                            'comment_id' => $data['comment_id'],
                            'user_id' => $data['user_id'],
                            'type' => $data['type'],
                            'body' => $data['body'],
                            'is_viewed' => $data['is_viewed'] ?? false,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
