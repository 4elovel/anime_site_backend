<?php

namespace Liamtseva\Cinema\Policies;

use Liamtseva\Cinema\Models\SearchHistory;
use Liamtseva\Cinema\Models\User;

class SearchHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SearchHistory $searchHistory): bool
    {
        return true;
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
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SearchHistory $searchHistory): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SearchHistory $searchHistory): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SearchHistory $searchHistory): bool
    {
        return true;
    }
}
