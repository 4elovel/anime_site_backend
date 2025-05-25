<?php

namespace AnimeSite\Actions\Tariffs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tariff;

class DeleteTariff
{
    /**
     * Видалити тариф.
     *
     * @param Tariff $tariff
     * @return void
     */
    public function __invoke(Tariff $tariff): void
    {
        Gate::authorize('delete', $tariff);

        DB::transaction(function () use ($tariff) {
            // Перевіряємо, чи є активні підписки на цей тариф
            if ($tariff->subscriptions()->where('is_active', true)->exists()) {
                throw new \Exception('Неможливо видалити тариф, оскільки на нього є активні підписки.');
            }
            
            // Видаляємо тариф
            $tariff->delete();
        });
    }
}
