<?php

namespace App\Http\Controllers\Api\User\ContentPage;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentPage\ContentPageResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\ContentPage\ContentPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentPageController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'title_ar', 'title_en', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $query = ContentPage::query()->active();

        $sortBy = $request->input('sort_by');
        if ($sortBy && in_array($sortBy, self::SORT_ALLOWED, true)) {
            $sortOrder = strtolower($request->input('sort_order', 'asc'));
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (ContentPage $page) => ContentPageResource::make($page)->resolve($request));

        return response()->json($paginator);
    }

    public function show(Request $request, ContentPage $content_page): JsonResponse
    {
        if (! $content_page->is_active) {
            abort(404);
        }

        return ContentPageResource::make($content_page)->response($request);
    }
}
