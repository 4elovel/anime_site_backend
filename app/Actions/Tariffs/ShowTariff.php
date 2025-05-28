<?php

namespace AnimeSite\Actions\Tariffs;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tariff;

class ShowTariff
{
    /**
     * Отримати конкретний тариф.
     *
     * @param Tariff $tariff
     * @return Tariff
     */
    public function __invoke(Tariff $tariff): Tariff
    {
        // Перевіряємо, чи тариф активний (для звичайних користувачів)
        if (!$tariff->is_active && !Gate::allows('viewInactive', Tariff::class)) {
            abort(404, 'Тариф не знайдено');
        }
        
        return $tariff->loadMissing(['subscriptions']);
    }
}
