<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 2);
        $keywordName = request()->get('keywordname');
        $keywordSlug = request()->get('keywordslug');

        $cacheKey = "category_index_page_{$page}_per_page_{$perPage}_keywordname_{$keywordName}_keywordslug_{$keywordSlug}";

        $category = Cache::tags(['category_index'])->rememberForever($cacheKey, function ()  use ($perPage, $keywordName, $keywordSlug) {

            return Category::when($keywordName, function ($query) use ($keywordName) {

                return $query->where('name', 'like', '%' . $keywordName . '%');
            })->when($keywordSlug, function ($query) use ($keywordSlug) {

                return $query->where('slug', 'like', '%' . $keywordSlug . '%');
            })->paginate($perPage);
        });

        return response()->json([
            'status' => true,
            'message' => __('messages.success'),
            'data' =>  CategoryResource::collection($category)->toResponse(app('Request'))->getData(true)
        ], 200);

    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug')
        ]);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.create'),
            'data' => new CategoryResource($category)

        ], 201);
    }

    public function edit(UpdateCategoryRequest $request, $id)
    {

        $category = $this->getCategoryById($id);

        $category->update([
            'name' => $request->get('name', $category->name),
            'slug' => $request->get('slug', $category->slug)
        ]);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.update'),
            'data' => new CategoryResource($category)
        ]);
    }

    public function destroy($id)
    {
        $category = $this->getCategoryById($id);

        $category->delete();

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.delete'),
        ], 200);
    }

    protected function getCategoryById($id)
    {
        return Cache::rememberForever('category_' . $id, function () use ($id) {

            return Category::findOrFail($id);
        });
    }
}
