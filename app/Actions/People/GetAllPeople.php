<?php

namespace AnimeSite\Actions\People;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\DTOs\People\PersonIndexDTO;
use AnimeSite\Models\Person;

class GetAllPeople
{
    /**
     * Отримати список людей з пошуком, фільтрацією, сортуванням та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        // Gate::authorize('viewAny', Person::class); // Дозволяємо перегляд персон без авторизації

        // Створюємо DTO з параметрів запиту
        $dto = PersonIndexDTO::fromRequest($request);

        // Використовуємо екшин GetFilteredPeople для отримання результатів
        return app(GetFilteredPeople::class)($dto);
    }
}
