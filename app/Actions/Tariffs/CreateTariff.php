<?php

namespace AnimeSite\Actions\Tariffs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tariff;

class CreateTariff
{
    /**
     * Створити новий тариф.
     *
     * @param array{
     *     slug: string,
     *     name: string,
     *     description: string,
     *     price: float,
     *     currency: string,
     *     duration_days: int,
     *     features?: array,
     *     is_active?: bool,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null
     * } $data
     * @return Tariff
     */
    public function __invoke(array $data): Tariff
    {
        Gate::authorize('create', Tariff::class);

        return DB::transaction(function () use ($data) {
            // Встановлюємо значення за замовчуванням
            $data['is_active'] = $data['is_active'] ?? true;
            
            // Створюємо тариф
            $tariff = Tariff::create($data);
            
            return $tariff->loadMissing(['subscriptions']);
        });
    }
}
