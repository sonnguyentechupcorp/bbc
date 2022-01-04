<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\HttpClientController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UserController;
use App\Observers\CategoryObserver;
use Illuminate\Support\Facades\Route;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Mail;
//use App\Http\Resources\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//User
Route::group(['middleware' => ['auth:sanctum'], 'as' => 'user.'], function () {

    Route::get('/users', [UserController::class, 'index'])->name('index');
    Route::post('/user', [UserController::class, 'store'])->name('store');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('show');
    Route::put('/user/{id}', [UserController::class, 'edit'])->name('edit');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('destroy');
});

//Posts
Route::group(['middleware' => ['auth:sanctum'], 'as' => 'post.'], function () {

    Route::get('/posts', [PostsController::class, 'index'])->name('index');
    Route::post('/post', [PostsController::class, 'store'])->name('store');
    Route::get('/post/{id}', [PostsController::class, 'show'])->name('show');
    Route::put('/post/{id}', [PostsController::class, 'edit'])->name('edit');
    Route::delete('/post/{id}', [PostsController::class, 'destroy'])->name('destroy');

    // Create get posts in category by category slug feature.
    Route::get('/category/posts', [PostsController::class, 'getPostsbyCategorySlug']);
    // Create get posts in category by category slug, name feature.
    Route::get('/category/posts/search', [PostsController::class, 'getPostsbyCategorySlugName']);

});

//Categories
Route::group(['middleware' => ['auth:sanctum'], 'as' => 'category.'], function () {

    Route::get('/category', [CategoryController::class, 'index'])->name('index');
    Route::post('/category', [CategoryController::class, 'store'])->name('store');
    Route::put('/category/{id}', [CategoryController::class, 'edit'])->name('edit');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

//Auth
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');
Route::post('/register', [RegisterController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

//HttpClient
Route::get('/fetchpost', [HttpClientController::class, 'getAllPost']);
Route::get('/fetchpost/{id}', [HttpClientController::class, 'getPostById']);
Route::post('/fetchpost', [HttpClientController::class, 'addPost']);
Route::put('/fetchpost/{id}', [HttpClientController::class, 'updatePost']);
Route::delete('/fetchpost/{id}', [HttpClientController::class, 'deletePost']);

//Test
Route::get('/test', [PostsController::class, 'abc']);


