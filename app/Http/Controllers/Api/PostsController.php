<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostsRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostsController extends Controller
{
    // Create get posts in category by category slug feature.
    public function getPostsbyCategorySlug()
    {
        $slug = request()->get('slug');
        $category = Category::where('slug', $slug)->first();

        if (empty($category)) {

            return response()->json([
                'status' => false,
                'message' => __('messages.categoryslug'),
            ], 404);
        }

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 2);
        $keyword = '';
        $cacheKey = "post_index_page_{$page}_per_page_{$perPage}_keyword_{$keyword}_in_category_{$slug}";
        $posts = Cache::tags(['post_index'])->rememberForever($cacheKey, function ()  use ($perPage, $category) {

            return $category->posts()->paginate($perPage);
        });

        return response()->json([
            'status' => true,
            'message' => __('messages.success'),
            'data' => $posts
        ], 200);
    }

    // Create get posts in category by category slug, name feature.
    // select * from posts where id in (Select post_id from category_post where category_id in (select id from categories where slug like '%c%'))
    public function getPostsbyCategorySlugName()
    {
        $slug = request()->get('slug');
        $name = request()->get('name');

        if (empty($slug) && empty($name)) {

            return response()->json([
                'status' => false,
                'message' => __('messages.category'),
            ], 404);
        }

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 2);
        $keyword = '';

        //$postCollection = DB::select("select * from posts where id in (Select post_id from category_post where category_id in (select id from categories where slug like '%$slug%' and name like '%$name%'))");
        $postCollection = Post::select('posts.*')
            ->join('category_post', 'category_post.post_id', '=', 'posts.id')
            ->join('categories', 'categories.id', '=', 'category_post.category_id')
            ->where('categories.slug', 'like', '%' . $slug . '%')
            ->where('categories.name', 'like', '%' . $name . '%')
            ->paginate($perPage);

        $cacheKey = "post_index_page_{$page}_per_page_{$perPage}_keyword_{$keyword}_in_category_{$slug}_in_category_{$name}";
        $posts = Cache::tags(['post_index_search'])->rememberForever($cacheKey, function ()  use ($perPage, $postCollection) {

            return $postCollection;
        });

        return response()->json([
            'status' => true,
            'message' => __('messages.success'),
            'data' => $posts
        ], 200);
    }

    public function index()
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 2);
        $keyword = request()->get('keyword');

        $cacheKey = "post_index_page_{$page}_per_page_{$perPage}_keyword_{$keyword}";

        $posts = Cache::tags(['post_index'])->rememberForever($cacheKey, function ()  use ($perPage, $keyword) {

            return Post::when($keyword, function ($query) use ($keyword) {

                return $query->where('title', 'like', '%' . $keyword . '%');
            })->paginate($perPage);
        });

        return response()->json([
            'status' => true,
            'message' => __('messages.success'),
            'data' => PostResource::collection($posts)->toResponse(app('request'))->getData(true)
        ], 200);
    }

    public function store(PostsRequest $request)
    {
        $post = Post::create([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'author_id' => $request->input('author_id'),
        ]);

        $this->getCategoryInPostContent($post);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.create'),
            'data' => new PostResource($post)
        ], 201);
    }

    public function show($id)
    {
        $post = $this->getPostById($id);

        return response()->json([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.informationPost'),
            'data' => new PostResource($post)
        ], 200);
    }

    public function edit(UpdatePostRequest $request, $id)
    {
        $post = $this->getPostById($id);

        $image = $request->avatar;
        if (!empty($image)) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploadFeatureImgPosts'), $imageName);
            $newAvatarUrl = "upload/" . $imageName;
        }

        $post->update([
            'title' => $request->get('title', $post->title),
            'body' => $request->get('body', $post->body),
            'author_id' => $request->get('author_id', $post->author_id),
            'feature_img' => empty($newAvatarUrl) ? $post->feature_img : $newAvatarUrl
        ]);

        $post->categories()->sync($request->categories ? $request->categories : $post->categories);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.update'),
            'data' => new PostResource($post)
        ]);
    }

    public function destroy($id)
    {
        $post = $this->getPostById($id);

        $post->delete();

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.delete'),
        ], 200);
    }

    protected function getPostById($id)
    {
        return Cache::rememberForever('post_' . $id, function () use ($id) {

            return Post::findOrFail($id);
        });
    }

    protected function getCategoryInPostContent(Post $post)
    {

        $categories = Category::all();

        $result = [];

        foreach ($categories as $category) {
            if (
                \str_contains($post->title, $category->name) ||
                \str_contains($post->body, $category->name)

            ) {

                $post->categories()->attach($category);

                $result[] = $category;
            }
        }

        return $result;
    }
}
