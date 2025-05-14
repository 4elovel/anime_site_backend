<?php

namespace AnimeSite\Actions\People;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Person;

class DeletePerson
{
    public function __invoke(Person $person): void
    {
        Gate::authorize('delete', $person);
        DB::transaction(fn () => $person->delete());
    }
}
