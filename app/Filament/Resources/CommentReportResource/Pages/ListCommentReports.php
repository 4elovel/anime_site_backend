<?php

namespace AnimeSite\Filament\Resources\CommentReportResource\Pages;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\CommentReportType;
use AnimeSite\Filament\Resources\CommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommentReports extends ListRecords
{
    protected static string $resource = CommentReportResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Всі'),

            'insult' => Tab::make('Осквернення користувачів')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::INSULT->value)),

            'flood_offtop_meaningless' => Tab::make('Флуд / Оффтоп / Коментар без змісту')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::FLOOD_OFFTOP_MEANINGLESS->value)),

            'ad_spam' => Tab::make('Реклама / Спам')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::AD_SPAM->value)),

            'spoiler' => Tab::make('Спойлер')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::SPOILER->value)),

            'provocation_conflict' => Tab::make('Провокації / Конфлікти')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::PROVOCATION_CONFLICT->value)),

            'inappropriate_language' => Tab::make('Ненормативна лексика')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::INAPPROPRIATE_LANGUAGE->value)),

            'forbidden_unnecessary_content' => Tab::make('Заборонений / Непотрібний контент')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::FORBIDDEN_UNNECESSARY_CONTENT->value)),

            'meaningless_empty_topic' => Tab::make('Безглузда / Порожня тема')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::MEANINGLESS_EMPTY_TOPIC->value)),

            'duplicate_topic' => Tab::make('Дублікат теми')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', CommentReportType::DUPLICATE_TOPIC->value)),
        ];

    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
