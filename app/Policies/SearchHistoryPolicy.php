<?php

namespace AnimeSite\Policies;

use AnimeSite\Models\SearchHistory;
use AnimeSite\Models\User;

class SearchHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SearchHistory $searchHistory): bool
    {
        return $user->id === $searchHistory->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SearchHistory $searchHistory): bool
    {
        return $user->id === $searchHistory->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SearchHistory $searchHistory): bool
    {
        return $user->id === $searchHistory->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SearchHistory $searchHistory): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SearchHistory $searchHistory): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }
}
