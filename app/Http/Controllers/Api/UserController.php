<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{

    public function index()
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 2);
        $keyword = request()->get('keyword');

        $cacheKey = "user_index_page_{$page}_per_page_{$perPage}_keyword_{$keyword}";

        $users = Cache::tags(['user_index'])->rememberForever($cacheKey, function () use ($perPage, $keyword) {

            return User::when($keyword, function ($query) use ($keyword) {

                return $query->where('name', 'like', '%' . $keyword . '%');
            })->paginate($perPage);
        });

        return response()->json([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.success'),
            'data' => UserResource::collection($users)->toResponse(app('Request'))->getData(true)
        ], 200);


    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => ["User"],
            'gender' => $request->input('gender'),
            'birth_date' => $request->input('birth_date'),
            'password' => Hash::make($request->password),
        ]);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.create'),
            'data' => new UserResource($user)
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $user = $this->getUserById($id);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.informationUser'),
            'data' => new UserResource($user)
        ], 200);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    public function edit(UpdateUserRequest $request, $id)
    {
        $user = $this->getUserById($id);

        $image = $request->avatar;
        if (!empty($image)) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload'), $imageName);
            $newAvatarUrl = "upload/" . $imageName;
        }

        $user->update([
            'name' => $request->get('name', $user->name),
            'gender' => $request->get('gender', $user->gender),
            'birth_date' => $request->get('birth_date', $user->birth_date),
            'avatar' => empty($newAvatarUrl) ? $user->avatar : $newAvatarUrl
        ]);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.update'),
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $user = $this->getUserById($id);

        $user->delete();

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.delete'),
        ], 200);
    }

    /**
     * @param  int  $id
     * @return \App\Models\User
     */
    protected function getUserById($id)
    {
        return Cache::rememberForever('user_' . $id, function () use ($id) {
            return User::findOrFail($id);
        });
    }
}
