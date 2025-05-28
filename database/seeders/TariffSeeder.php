<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use AnimeSite\Models\Tariff;

class TariffSeeder extends Seeder
{
    /**
     * Get features for a specific tariff
     */
    private function getFeaturesForTariff(string $name): array
    {
        $baseFeatures = ['quality_sd', 'unlimited_viewing'];
        $standardFeatures = array_merge($baseFeatures, ['quality_hd', 'multiple_devices']);
        $premiumFeatures = array_merge($standardFeatures, ['no_ads', 'offline_viewing']);
        $ultraFeatures = array_merge($premiumFeatures, ['quality_4k', 'premium_content', 'priority_support', 'early_access', 'family_sharing']);

        if (str_contains($name, 'Базовий')) {
            return $baseFeatures;
        } elseif (str_contains($name, 'Стандарт')) {
            return $standardFeatures;
        } elseif (str_contains($name, 'Преміум')) {
            return $premiumFeatures;
        } elseif (str_contains($name, 'Ультра')) {
            return $ultraFeatures;
        } else {
            // For other types, randomly select features
            $allFeatures = [
                'quality_sd', 'quality_hd', 'quality_4k', 'multiple_devices',
                'no_ads', 'offline_viewing', 'early_access', 'premium_content',
                'priority_support', 'exclusive_events', 'family_sharing', 'unlimited_viewing'
            ];

            // Select a random number of features (3-8)
            $count = rand(3, 8);
            shuffle($allFeatures);
            return array_slice($allFeatures, 0, $count);
        }
    }

    public function run(): void
    {
        // Спочатку створюємо основні тарифи
        $mainTariffs = [
            'Базовий' => 'Базовий тариф для початківців',
            'Преміум' => 'Преміум тариф з додатковими можливостями',
            'Ультра' => 'Ультра тариф з повним доступом до всіх функцій'
        ];

        // Створюємо або оновлюємо основні тарифи
        foreach ($mainTariffs as $name => $description) {
            $slug = Str::slug($name);

            Tariff::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $description,
                    'price' => rand(4999, 19999) / 100,
                    'currency' => 'UAH',
                    'duration_days' => 30,
                    'features' => $this->getFeaturesForTariff($name),
                    'is_active' => true,
                    'meta_title' => "Тариф {$name} - Аніме Сервіс",
                    'meta_description' => "Підписка на тариф {$name} - отримайте доступ до найкращого аніме контенту",
                    'meta_image' => 'https://via.placeholder.com/1200x630.png/009977?text=tariff'
                ]
            );

            $this->command->info("Tariff '{$name}' created or updated.");
        }

        // Додатково створюємо ще кілька тарифів
        $additionalCount = 3;
        $this->command->info("Creating {$additionalCount} additional tariffs...");

        for ($i = 0; $i < $additionalCount; $i++) {
            $tariff = Tariff::factory()->create();
            $this->command->info("Created additional tariff: {$tariff->name} (slug: {$tariff->slug})");
        }
    }
}
