<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ApiPaginationFilters
{
    /**
     * Default per_page when not in request.
     */
    protected function defaultPerPage(): int
    {
        return 15;
    }

    /**
     * Max per_page allowed.
     */
    protected function maxPerPage(): int
    {
        return 100;
    }

    /**
     * Get validated per_page from request.
     */
    protected function getPerPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', $this->defaultPerPage());

        return min(max($perPage, 1), $this->maxPerPage());
    }

    /**
     * Apply sort to query if request has sort_by and allowed columns are provided.
     * Uses request keys: sort_by, sort_order (asc|desc).
     *
     * @param  array<string>  $allowedColumns
     */
    protected function applySort(Builder $query, Request $request, array $allowedColumns): Builder
    {
        $sortBy = $request->input('sort_by');
        if (! $sortBy || ! in_array($sortBy, $allowedColumns, true)) {
            return $query;
        }

        $sortOrder = strtolower($request->input('sort_order', 'asc'));

        return $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
    }

    /**
     * Parse boolean from request (supports "1", "true", "yes", "0", "false", "no").
     */
    protected function parseBool(Request $request, string $key): ?bool
    {
        if (! $request->has($key)) {
            return null;
        }

        return filter_var($request->input($key), FILTER_VALIDATE_BOOLEAN);
    }
}
