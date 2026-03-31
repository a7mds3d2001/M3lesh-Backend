<?php

namespace App\Http\Controllers\Api\Admin\ContentPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentPage\StoreContentPageRequest;
use App\Http\Requests\ContentPage\UpdateContentPageRequest;
use App\Http\Resources\ContentPage\ContentPageResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\ContentPage\ContentPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentPageController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'title_ar', 'title_en', 'is_active', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ContentPage::class);

        $query = ContentPage::query()->with(['creator', 'updater']);

        if ($request->has('is_active')) {
            $query->where('is_active', $this->parseBool($request, 'is_active'));
        }

        $search = $request->input('search') ?? $request->input('q');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderBy('updated_at', 'desc');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (ContentPage $page) => ContentPageResource::make($page)->resolve($request));

        return response()->json($paginator);
    }

    public function store(StoreContentPageRequest $request): JsonResponse
    {
        $this->authorize('create', ContentPage::class);

        $validated = $request->validated();
        $validated['is_active'] = $validated['is_active'] ?? true;

        $page = ContentPage::create($validated);
        $page->load(['creator', 'updater']);

        return ContentPageResource::make($page)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, ContentPage $content_page): JsonResponse
    {
        $this->authorize('view', $content_page);
        $content_page->load(['creator', 'updater']);

        return ContentPageResource::make($content_page)->response($request);
    }

    public function update(UpdateContentPageRequest $request, ContentPage $content_page): JsonResponse
    {
        $this->authorize('update', $content_page);

        $validated = $request->validated();
        unset($validated['created_by']);
        $content_page->update($validated);

        return ContentPageResource::make($content_page->fresh(['creator', 'updater']))->response($request);
    }

    public function destroy(ContentPage $content_page): JsonResponse
    {
        $this->authorize('delete', $content_page);
        $content_page->delete();

        return response()->json(null, 204);
    }
}
