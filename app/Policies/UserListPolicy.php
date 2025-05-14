<?php

namespace AnimeSite\Policies;

use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

class UserListPolicy
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
    public function view(User $user, UserList $userList): bool
    {
        return $user->id === $user->user_id || $user->isAdmin() || $user->isModerator();
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
    public function update(User $user, UserList $userList): bool
    {
        return $user->id === $user->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserList $userList): bool
    {
        return $user->id === $user->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserList $userList): bool
    {
        return $user->id === $user->user_id || $user->isAdmin() || $user->isModerator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserList $userList): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }
}
