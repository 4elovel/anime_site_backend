<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use AnimeSite\Enums\TariffFeature;
use AnimeSite\Models\Tariff;

/**
 * @extends Factory<Tariff>
 */
class TariffFactory extends Factory
{
    public function definition(): array
    {
        // Base tariff types
        $tariffTypes = ['Стандарт', 'Річний', 'Сімейний', 'Студентський', 'Корпоративний'];

        // Generate a name with a random suffix to ensure uniqueness
        $baseName = $tariffTypes[array_rand($tariffTypes)];
        $name = $baseName . ' ' . $this->faker->word . ' ' . uniqid();

        $slug = Str::slug($name);

        return [
            'slug' => $slug,
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 49, 299),
            'currency' => 'UAH',
            'duration_days' => $this->faker->randomElement([30, 90, 180, 365]),
            'features' => $this->getFeatures($name),
            'is_active' => true,
            'meta_title' => "Тариф {$name} - Аніме Сервіс",
            'meta_description' => "Підписка на тариф {$name} - отримайте доступ до найкращого аніме контенту",
            'meta_image' => $this->faker->imageUrl(1200, 630),
        ];
    }

    /**
     * Get features based on tariff name
     */
    private function getFeatures(string $name): array
    {
        $baseFeatures = [TariffFeature::QUALITY_SD->value, TariffFeature::UNLIMITED_VIEWING->value];
        $standardFeatures = array_merge($baseFeatures, [TariffFeature::QUALITY_HD->value, TariffFeature::MULTIPLE_DEVICES->value]);
        $premiumFeatures = array_merge($standardFeatures, [TariffFeature::NO_ADS->value, TariffFeature::OFFLINE_VIEWING->value]);
        $ultraFeatures = array_merge($premiumFeatures, [TariffFeature::QUALITY_4K->value, TariffFeature::PREMIUM_CONTENT->value, TariffFeature::PRIORITY_SUPPORT->value, TariffFeature::EARLY_ACCESS->value, TariffFeature::FAMILY_SHARING->value]);

        // Визначаємо функції в залежності від типу тарифу
        if (str_contains($name, 'Базовий')) {
            return $baseFeatures;
        } elseif (str_contains($name, 'Стандарт')) {
            return $standardFeatures;
        } elseif (str_contains($name, 'Преміум') || str_contains($name, 'Річний')) {
            return $premiumFeatures;
        } elseif (str_contains($name, 'Ультра')) {
            return $ultraFeatures;
        } elseif (str_contains($name, 'Сімейний')) {
            return array_merge($premiumFeatures, [TariffFeature::FAMILY_SHARING->value]);
        } elseif (str_contains($name, 'Студентський')) {
            return array_merge($standardFeatures, [TariffFeature::NO_ADS->value]);
        } elseif (str_contains($name, 'Корпоративний')) {
            return array_merge($ultraFeatures, [TariffFeature::PRIORITY_SUPPORT->value]);
        } else {
            // Для інших типів випадково вибираємо функції
            $allFeatures = [
                TariffFeature::QUALITY_SD->value,
                TariffFeature::QUALITY_HD->value,
                TariffFeature::QUALITY_4K->value,
                TariffFeature::MULTIPLE_DEVICES->value,
                TariffFeature::NO_ADS->value,
                TariffFeature::OFFLINE_VIEWING->value,
                TariffFeature::EARLY_ACCESS->value,
                TariffFeature::PREMIUM_CONTENT->value,
                TariffFeature::PRIORITY_SUPPORT->value,
                TariffFeature::EXCLUSIVE_EVENTS->value,
                TariffFeature::FAMILY_SHARING->value,
                TariffFeature::UNLIMITED_VIEWING->value
            ];

            // Вибираємо випадкову кількість функцій (3-8)
            $count = rand(3, 8);
            shuffle($allFeatures);
            return array_slice($allFeatures, 0, $count);
        }
    }

    /**
     * Indicate that the tariff is inactive
     */
    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
