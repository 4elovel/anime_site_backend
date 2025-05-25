<?php

namespace AnimeSite\Actions\Search;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\SearchHistory;

class StoreSearchHistory
{
    /**
     * Зберегти пошуковий запит в історії пошуку користувача.
     *
     * @param array{
     *     query: string,
     *     type?: string|null
     * } $data
     * @return SearchHistory|null
     */
    public function __invoke(array $data): ?SearchHistory
    {
        // Якщо користувач не авторизований, не зберігаємо історію
        if (!Auth::check()) {
            return null;
        }

        $userId = Auth::id();
        $query = $data['query'];

        return DB::transaction(function () use ($userId, $query) {
            // Перевіряємо, чи вже є такий запит в історії
            $existingHistory = SearchHistory::where('user_id', $userId)
                ->where('query', $query)
                ->first();

            if ($existingHistory) {
                // Якщо запит вже є в історії, оновлюємо дату
                $existingHistory->touch();
                return $existingHistory;
            }

            // Інакше створюємо новий запис
            return SearchHistory::create([
                'user_id' => $userId,
                'query' => $query,
            ]);
        });
    }
}
