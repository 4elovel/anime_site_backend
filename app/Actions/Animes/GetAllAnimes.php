<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Enums\Country;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;
use AnimeSite\Enums\VideoQuality;
use AnimeSite\Models\Anime;

class GetAllAnimes
{
    /**
     * Отримати список аніме з пошуком, фільтрацією, сортуванням та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
         Gate::authorize('viewAny', Anime::class); // Тимчасово вимкнено для тестування API

        $perPage = (int) $request->input('per_page', 15);
        $query = Anime::query();

        // Пошук
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            // Якщо доступний повнотекстовий пошук, використовуємо його
            if (config('app.fulltext_search_enabled', false)) {
                $query->fullTextSearch($searchTerm);
            } else {
                // Інакше використовуємо звичайний пошук по ключовим полям
                $query->search($searchTerm, ['name', 'description']);
            }
        }

        // Фільтрація за типом
        if ($request->filled('kind')) {
            $kind = Kind::tryFrom($request->input('kind'));
            if ($kind) {
                $query->ofKind($kind);
            }
        }

        // Фільтрація за статусом
        if ($request->filled('status')) {
            $status = Status::tryFrom($request->input('status'));
            if ($status) {
                $query->withStatus($status);
            }
        }

        // Фільтрація за сезоном
        if ($request->filled('period')) {
            $period = Period::tryFrom($request->input('period'));
            if ($period) {
                $query->ofPeriod($period);
            }
        }

        // Фільтрація за рейтингом IMDB
        if ($request->filled('imdb_score')) {
            $query->withImdbScoreGreaterThan((float) $request->input('imdb_score'));
        }

        // Фільтрація за країною
        if ($request->filled('country')) {
            $country = Country::tryFrom($request->input('country'));
            if ($country) {
                $query->fromCountry($country);
            }
        }

        // Фільтрація за студією
        if ($request->filled('studio_id')) {
            $query->fromStudio($request->input('studio_id'));
        }

        // Фільтрація за тегом
        if ($request->filled('tag_id')) {
            $query->withTag($request->input('tag_id'));
        }

        // Фільтрація за людиною
        if ($request->filled('person_id')) {
            $query->withPerson($request->input('person_id'));
        }

        // Фільтрація за роком випуску
        if ($request->filled('year')) {
            $query->releasedInYear((int) $request->input('year'));
        }

        // Фільтрація за кількістю епізодів
        if ($request->filled('episodes_count')) {
            $query->withEpisodesCount((int) $request->input('episodes_count'));
        }

        // Фільтрація за тривалістю
        if ($request->filled('duration')) {
            $query->withDuration((int) $request->input('duration'));
        }

        // Фільтрація за датою виходу
        if ($request->filled('air_date_from') && $request->filled('air_date_to')) {
            $query->airedBetween(
                $request->input('air_date_from'),
                $request->input('air_date_to')
            );
        }

        // Фільтрація за віковим обмеженням
        if ($request->filled('restricted_rating')) {
            $restrictedRating = RestrictedRating::tryFrom($request->input('restricted_rating'));
            if ($restrictedRating) {
                $query->withRestrictedRating($restrictedRating);
            }
        }

        // Фільтрація за джерелом
        if ($request->filled('source')) {
            $source = Source::tryFrom($request->input('source'));
            if ($source) {
                $query->fromSource($source);
            }
        }

        // Фільтрація за якістю відео
        if ($request->filled('video_quality')) {
            $videoQuality = VideoQuality::tryFrom($request->input('video_quality'));
            if ($videoQuality) {
                $query->withVideoQuality($videoQuality);
            }
        }

        // Фільтрація за популярністю
        if ($request->boolean('popular')) {
            $minRatings = $request->input('min_ratings', 10);
            $query->popular((int) $minRatings);
        }

        // Фільтрація за найвищим рейтингом
        if ($request->boolean('top_rated')) {
            $minScore = $request->input('min_score', 7.0);
            $query->topRated((float) $minScore);
        }

        // Фільтрація за нещодавно доданими
        if ($request->boolean('recently_added')) {
            $days = $request->input('days', 30);
            $query->addedInLastDays((int) $days);
        }

        // Фільтрація за нещодавно оновленими
        if ($request->boolean('recently_updated')) {
            $days = $request->input('days', 30);
            $query->updatedInLastDays((int) $days);
        }

        // Фільтрація за схожими аніме
        if ($request->filled('similar_to')) {
            $query->similarTo($request->input('similar_to'));
        }

        // Фільтрація за пов'язаними аніме
        if ($request->filled('related_to')) {
            $query->relatedTo($request->input('related_to'));
        }

        // Сортування
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = 'asc';

            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }

            switch ($sort) {
                case 'title':
                    $query->orderBy('title', $direction);
                    break;
                case 'original_title':
                    $query->orderBy('original_title', $direction);
                    break;
                case 'imdb_score':
                    $query->orderBy('imdb_score', $direction);
                    break;
                case 'first_air_date':
                    $query->orderBy('first_air_date', $direction);
                    break;
                case 'last_air_date':
                    $query->orderBy('last_air_date', $direction);
                    break;
                case 'episodes_count':
                    $query->orderBy('episodes_count', $direction);
                    break;
                case 'duration':
                    $query->orderBy('duration', $direction);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $direction);
                    break;
                case 'updated_at':
                    $query->orderBy('updated_at', $direction);
                    break;
                default:
                    // За замовчуванням сортуємо за датою створення (нові спочатку)
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // За замовчуванням сортуємо за датою створення (нові спочатку)
            $query->orderBy('created_at', 'desc');
        }

        // Завантажуємо зв'язані дані
        $query->with(['studio', 'tags', 'ratings']);

        // Додаємо середній рейтинг користувачів
        $query->withAvg('ratings', 'number');

        // Пагінація
        return $query->paginate($perPage);
    }
}
