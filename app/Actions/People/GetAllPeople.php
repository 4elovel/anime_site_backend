<?php

namespace AnimeSite\Actions\People;


use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Person;

class GetAllPeople
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Person::class);

        $perPage = (int) $request->input('per_page', 15);

        return Person::query()
            ->when($request->filled('type'), fn($q) =>
            $q->byType($request->input('type'))
            )
            ->when($request->filled('gender'), fn($q) =>
            $q->byGender($request->input('gender'))
            )
            ->when($request->filled('search'), fn($q) =>
            $q->search($request->input('search'))
            )
            ->with(['animes'])
            ->paginate($perPage);
    }
}
