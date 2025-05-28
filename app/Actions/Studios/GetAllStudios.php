<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\DTOs\Studios\StudioIndexDTO;
use AnimeSite\Models\Studio;

class GetAllStudios
{
    /**
     * Отримати список студій з пошуком, фільтрацією, сортуванням та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        // Gate::authorize('viewAny', Studio::class); // Дозволяємо перегляд студій без авторизації

        // Створюємо DTO з параметрів запиту
        $dto = StudioIndexDTO::fromRequest($request);

        // Використовуємо екшин GetFilteredStudios для отримання результатів
        return app(GetFilteredStudios::class)($dto);
    }
}
