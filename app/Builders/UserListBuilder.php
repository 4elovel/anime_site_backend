<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\UserListType;

class UserListBuilder extends Builder
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
     * Filter by list type
     *
     * @param UserListType $type
     * @return $this
     */
    public function ofType(UserListType $type): self
    {
        $this->where('type', $type->value);

        return $this;
    }

    /**
     * Filter by user
     *
     * @param string $userId
     * @param string|null $listableClass
     * @param UserListType|null $userListType
     * @return $this
     */
    public function forUser(
        string $userId,
        ?string $listableClass = null,
        ?UserListType $userListType = null
    ): self {
        $this->where('user_id', $userId)
            ->when($listableClass, function ($query) use ($listableClass) {
                $query->where('listable_type', $listableClass);
            })
            ->when($userListType, function ($query) use ($userListType) {
                $query->where('type', $userListType->value);
            });

        return $this;
    }

    /**
     * Filter by favorite lists
     *
     * @return $this
     */
    public function favorites(): self
    {
        $this->where('type', UserListType::FAVORITE->value);

        return $this;
    }

    /**
     * Filter by watching lists
     *
     * @return $this
     */
    public function watching(): self
    {
        $this->where('type', UserListType::WATCHING->value);

        return $this;
    }

    /**
     * Filter by planned lists
     *
     * @return $this
     */
    public function planned(): self
    {
        $this->where('type', UserListType::PLANNED->value);

        return $this;
    }

    /**
     * Filter by watched lists
     *
     * @return $this
     */
    public function watched(): self
    {
        $this->where('type', UserListType::WATCHED->value);

        return $this;
    }

    /**
     * Filter by stopped lists
     *
     * @return $this
     */
    public function stopped(): self
    {
        $this->where('type', UserListType::STOPPED->value);

        return $this;
    }

    /**
     * Filter by rewatching lists
     *
     * @return $this
     */
    public function rewatching(): self
    {
        $this->where('type', UserListType::REWATCHING->value);

        return $this;
    }

    /**
     * Filter by listable type
     *
     * @param string $type
     * @return $this
     */
    public function withListableType(string $type): self
    {
        $this->where('listable_type', $type);

        return $this;
    }

    /**
     * Filter by listable ID
     *
     * @param string $id
     * @return $this
     */
    public function withListableId(string $id): self
    {
        $this->where('listable_id', $id);

        return $this;
    }

    /**
     * Filter by recently added items
     *
     * @param int $days
     * @return $this
     */
    public function addedInLastDays(int $days = 30): self
    {
        $this->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');

        return $this;
    }

    /**
     * Filter by recently updated items
     *
     * @param int $days
     * @return $this
     */
    public function updatedInLastDays(int $days = 30): self
    {
        $this->where('updated_at', '>=', now()->subDays($days))
            ->orderByDesc('updated_at');

        return $this;
    }
}
