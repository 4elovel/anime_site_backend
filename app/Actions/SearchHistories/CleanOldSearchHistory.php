<?php

namespace AnimeSite\Actions\SearchHistories;

use AnimeSite\Models\SearchHistory;

class CleanOldSearchHistory
{
    /**
     * Очищає історію пошуку старшу за задану кількість днів для користувача.
     *
     * @param string $userId
     * @param int $days
     */
    public function __invoke(string $userId, int $days = 30): void
    {
        SearchHistory::cleanOldHistory($userId, $days);
    }
}
