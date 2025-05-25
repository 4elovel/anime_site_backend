<?php

namespace AnimeSite\Actions\Tariffs;

use Illuminate\Support\Collection;
use AnimeSite\Models\Tariff;

class CompareTariffs
{
    /**
     * Порівняти тарифи за їх ID.
     *
     * @param array $tariffIds
     * @return Collection
     */
    public function __invoke(array $tariffIds): Collection
    {
        // Отримуємо тарифи за їх ID
        $tariffs = Tariff::whereIn('id', $tariffIds)
            ->where('is_active', true)
            ->get();
        
        // Якщо тарифів менше 2, повертаємо порожню колекцію
        if ($tariffs->count() < 2) {
            return collect();
        }
        
        // Отримуємо всі унікальні функції з тарифів
        $allFeatures = $tariffs->pluck('features')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
        
        // Формуємо результат порівняння
        $result = $tariffs->map(function ($tariff) use ($allFeatures) {
            $tariffFeatures = collect($tariff->features);
            
            $featuresComparison = collect($allFeatures)->mapWithKeys(function ($feature) use ($tariffFeatures) {
                return [$feature => $tariffFeatures->contains($feature)];
            });
            
            return [
                'id' => $tariff->id,
                'slug' => $tariff->slug,
                'name' => $tariff->name,
                'description' => $tariff->description,
                'price' => $tariff->price,
                'formatted_price' => $tariff->formatted_price,
                'currency' => $tariff->currency,
                'duration_days' => $tariff->duration_days,
                'features' => $featuresComparison,
            ];
        });
        
        return $result;
    }
}
