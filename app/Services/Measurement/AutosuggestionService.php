<?php

namespace App\Services\Measurement;

use App\Exceptions\Autosuggestion\AutosuggestionTypeValueIsWrong;
use App\Models\Measurement;
use Illuminate\Support\Facades\Schema;

class AutosuggestionService
{
    public function suggestions(string $type, string $query): array
    {
        if (!$this->validateType($type)) {
            throw new AutosuggestionTypeValueIsWrong('Type value is incorrect');
        }

        return Measurement::withTrashed()
            ->where('netto', 0)
            ->where($type, 'like', '%'.$query.'%')
            ->distinct()
            ->pluck($type)
            ->toArray();
    }

    private function validateType(string $type): bool
    {
        $allColumns = Schema::getColumnListing('measurements');
        $availableColumns = array_diff($allColumns, ['id', 'notes', 'modified_by_id', 'deleted_at', 'created_at', 'updated_at']);

        return in_array($type, $availableColumns);
    }
}
