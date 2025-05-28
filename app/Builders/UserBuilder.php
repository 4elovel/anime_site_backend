<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\Role;

class UserBuilder extends Builder
{
    /**
     * Order by created date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByCreatedAt(string $direction = 'desc'): self
    {
        $this->orderBy('created_at', $direction);

        return $this;
    }

    /**
     * Order by updated date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByUpdatedAt(string $direction = 'desc'): self
    {
        $this->orderBy('updated_at', $direction);

        return $this;
    }

    /**
     * Filter by active status
     *
     * @param bool $active
     * @return $this
     */
    public function active(bool $active = true): self
    {
        $this->where('is_active', $active);

        return $this;
    }

    /**
     * Filter by users who allow adult content
     *
     * @return $this
     */
    public function allowedAdults(): self
    {
        $this->where('allow_adult', true);

        return $this;
    }

    /**
     * Filter by user role
     *
     * @param Role $role
     * @return $this
     */
    public function byRole(Role $role): self
    {
        $this->where('role', $role->value);

        return $this;
    }

    /**
     * Filter by admin users
     *
     * @return $this
     */
    public function admins(): self
    {
        $this->where('role', Role::ADMIN->value);

        return $this;
    }

    /**
     * Filter by moderator users
     *
     * @return $this
     */
    public function moderators(): self
    {
        $this->where('role', Role::MODERATOR->value);

        return $this;
    }

    /**
     * Filter by regular users
     *
     * @return $this
     */
    public function regularUsers(): self
    {
        $this->where('role', Role::USER->value);

        return $this;
    }

    /**
     * Filter by VIP users
     *
     * @return $this
     */
    public function vipCustomers(): self
    {
        $this->where('vip', true);

        return $this;
    }

    /**
     * Filter by users with a specific achievement
     *
     * @param string $achievementId
     * @return $this
     */
    public function withAchievement(string $achievementId): self
    {
        $this->whereHas('achievements', function ($query) use ($achievementId) {
            $query->where('achievements.id', $achievementId);
        });

        return $this;
    }

    /**
     * Filter by users who completed an achievement
     *
     * @param string $achievementId
     * @param int $maxCounts
     * @return $this
     */
    public function completedAchievement(string $achievementId, int $maxCounts): self
    {
        $this->whereHas('achievementsPivot', function ($query) use ($achievementId, $maxCounts) {
            $query->where('achievement_id', $achievementId)
                ->where('progress_count', '>=', $maxCounts);
        });

        return $this;
    }

    /**
     * Filter by users with active subscriptions
     *
     * @return $this
     */
    public function withActiveSubscription(): self
    {
        $this->whereHas('subscriptions', function ($query) {
            $query->where('is_active', true)
                ->where('end_date', '>=', now());
        });

        return $this;
    }

    /**
     * Filter by users with auto-renew subscriptions
     *
     * @return $this
     */
    public function withAutoRenewSubscription(): self
    {
        $this->whereHas('subscriptions', function ($query) {
            $query->where('auto_renew', true);
        });

        return $this;
    }

    /**
     * Filter by users who have watched a specific anime
     *
     * @param string $animeId
     * @return $this
     */
    public function watchedAnime(string $animeId): self
    {
        $this->whereHas('userLists', function ($query) use ($animeId) {
            $query->where('listable_type', 'Liamtseva\\Cinema\\Models\\Anime')
                ->where('listable_id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by users who have favorited a specific anime
     *
     * @param string $animeId
     * @return $this
     */
    public function favoritedAnime(string $animeId): self
    {
        $this->whereHas('favoriteAnimes', function ($query) use ($animeId) {
            $query->where('listable_id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by users who have rated a specific anime
     *
     * @param string $animeId
     * @return $this
     */
    public function ratedAnime(string $animeId): self
    {
        $this->whereHas('ratings', function ($query) use ($animeId) {
            $query->where('anime_id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by users who have commented on a specific anime
     *
     * @param string $animeId
     * @return $this
     */
    public function commentedOnAnime(string $animeId): self
    {
        $this->whereHas('comments', function ($query) use ($animeId) {
            $query->where('commentable_type', 'Liamtseva\\Cinema\\Models\\Anime')
                ->where('commentable_id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by users with a specific gender
     *
     * @param Gender $gender
     * @return $this
     */
    public function byGender(Gender $gender): self
    {
        $this->where('gender', $gender->value);

        return $this;
    }

    /**
     * Filter by users in a specific age range
     *
     * @param int $minAge
     * @param int $maxAge
     * @return $this
     */
    public function inAgeRange(int $minAge, int $maxAge): self
    {
        $minDate = now()->subYears($maxAge)->format('Y-m-d');
        $maxDate = now()->subYears($minAge)->format('Y-m-d');

        $this->whereBetween('birthday', [$minDate, $maxDate]);

        return $this;
    }

    /**
     * Filter by users who were recently active
     *
     * @param int $days
     * @return $this
     */
    public function recentlyActive(int $days = 7): self
    {
        $this->where('last_seen_at', '>=', now()->subDays($days));

        return $this;
    }

    /**
     * Filter by banned users
     *
     * @param bool $banned
     * @return $this
     */
    public function banned(bool $banned = true): self
    {
        $this->where('is_banned', $banned);

        return $this;
    }

    /**
     * Filter by users with specific notification preferences
     *
     * @param string $notificationType
     * @param bool $enabled
     * @return $this
     */
    public function withNotificationPreference(string $notificationType, bool $enabled = true): self
    {
        $this->where($notificationType, $enabled);

        return $this;
    }

    /**
     * Order by number of ratings
     *
     * @return $this
     */
    public function orderByRatingsCount(): self
    {
        $this->withCount('ratings')
            ->orderByDesc('ratings_count');

        return $this;
    }

    /**
     * Order by number of comments
     *
     * @return $this
     */
    public function orderByCommentsCount(): self
    {
        $this->withCount('comments')
            ->orderByDesc('comments_count');

        return $this;
    }

    /**
     * Search by name or email
     *
     * @param string $term
     * @return $this
     */
    public function search(string $term): self
    {
        $this->where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });

        return $this;
    }
}
