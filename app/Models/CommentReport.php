<?php

namespace AnimeSite\Models;

use Database\Factories\CommentReportFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AnimeSite\Builders\CommentReportBuilder;
use AnimeSite\Enums\CommentReportType;

/**
 * @mixin IdeHelperCommentReport
 */
class CommentReport extends Model
{
    /** @use HasFactory<CommentReportFactory> */
    use HasFactory, HasUlids;

    protected $casts = [
        'type' => CommentReportType::class,
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newEloquentBuilder($query): CommentReportBuilder
    {
        return new CommentReportBuilder($query);
    }
}
