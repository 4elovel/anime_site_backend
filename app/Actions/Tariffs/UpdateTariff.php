<?php

namespace AnimeSite\Actions\Tariffs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tariff;

class UpdateTariff
{
    /**
     * Оновити тариф.
     *
     * @param Tariff $tariff
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     description?: string,
     *     price?: float,
     *     currency?: string,
     *     duration_days?: int,
     *     features?: array,
     *     is_active?: bool,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null
     * } $data
     * @return Tariff
     */
    public function __invoke(Tariff $tariff, array $data): Tariff
    {
        Gate::authorize('update', $tariff);

        return DB::transaction(function () use ($tariff, $data) {
            // Оновлюємо тариф
            $tariff->update($data);
            
            return $tariff->loadMissing(['subscriptions']);
        });
    }
}
