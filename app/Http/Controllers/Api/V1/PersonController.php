<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\People\CreatePerson;
use AnimeSite\Actions\People\DeletePerson;
use AnimeSite\Actions\People\GetAllPeople;
use AnimeSite\Actions\People\GetFilteredPeople;
use AnimeSite\Actions\People\ShowPerson;
use AnimeSite\Actions\People\UpdatePerson;
use AnimeSite\DTOs\People\PersonIndexDTO;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StorePersonRequest;
use AnimeSite\Http\Requests\UpdatePersonRequest;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\PersonResource;
use AnimeSite\Models\Person;

class PersonController extends Controller
{
    /**
     * Отримати список людей з пошуком, фільтрацією, сортуванням та пагінацією.
     *
     * Параметри запиту:
     * - search: пошуковий запит
     * - types: типи людей (actor, director, producer, writer, voice_actor)
     * - genders: статі (male, female, other)
     * - is_active: фільтрувати за активністю
     * - is_published: фільтрувати за публікацією
     * - birthplace: місце народження
     * - birth_year: рік народження
     * - min_age: мінімальний вік
     * - max_age: максимальний вік
     * - anime_id: ID аніме, в якому бере участь людина
     * - character_name: ім'я персонажа, якого грає людина
     * - voice_person_id: ID актора озвучення
     * - selection_id: ID добірки, в якій є людина
     * - popular: фільтрувати за популярністю
     * - min_animes: мінімальна кількість аніме для популярних
     * - recently_added: нещодавно додані
     * - days: кількість днів для нещодавно доданих
     * - sort: поле для сортування (name, original_name, birthday, created_at, updated_at, popularity)
     * - direction: напрямок сортування (asc/desc)
     * - per_page: кількість елементів на сторінці
     * - page: номер сторінки
     *
     * @param Request $request
     * @param GetAllPeople $action
     * @return JsonResponse
     */
    public function index(Request $request, GetAllPeople $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => PersonResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Отримати список людей з розширеною фільтрацією.
     *
     * @param Request $request
     * @param GetFilteredPeople $action
     * @return JsonResponse
     */
    public function filter(Request $request, GetFilteredPeople $action): JsonResponse
    {
        $dto = PersonIndexDTO::fromRequest($request);
        $paginated = $action($dto);

        return response()->json([
            'data' => PersonResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request, CreatePerson $action): JsonResponse
    {
        $person = $action($request->validated());

        return response()->json(
            new PersonResource($person),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person, ShowPerson $action): JsonResponse
    {
        $person = $action($person);

        return response()->json(new PersonResource($person));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person, UpdatePerson $action): JsonResponse
    {
        $person = $action($person, $request->validated());

        return response()->json(new PersonResource($person));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person, DeletePerson $action): JsonResponse
    {
        $action($person);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get animes for a person.
     */
    public function animes(Person $person): JsonResponse
    {
        $animes = $person->animes()->paginate();

        return response()->json([
            'data' => AnimeResource::collection($animes),
            'meta' => [
                'current_page' => $animes->currentPage(),
                'last_page' => $animes->lastPage(),
                'per_page' => $animes->perPage(),
                'total' => $animes->total(),
            ],
        ]);
    }
}
