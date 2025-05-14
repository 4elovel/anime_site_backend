<?php

namespace AnimeSite\Policies;

use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;

class WatchHistoryPolicy
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
    public function view(User $user, WatchHistory $watchHistory): bool
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
    public function update(User $user, WatchHistory $watchHistory): bool
    {
        return $user->id === $watchHistory->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WatchHistory $watchHistory): bool
    {
        return $user->id === $watchHistory->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WatchHistory $watchHistory): bool
    {
        return $user->id === $watchHistory->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WatchHistory $watchHistory): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }
}
