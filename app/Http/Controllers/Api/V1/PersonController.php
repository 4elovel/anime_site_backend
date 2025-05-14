<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\People\CreatePerson;
use AnimeSite\Actions\People\DeletePerson;
use AnimeSite\Actions\People\GetAllPeople;
use AnimeSite\Actions\People\ShowPerson;
use AnimeSite\Actions\People\UpdatePerson;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StorePersonRequest;
use AnimeSite\Http\Requests\UpdatePersonRequest;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\PersonResource;
use AnimeSite\Models\Person;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
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
