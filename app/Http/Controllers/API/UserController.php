<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserStoreRequest;
use App\Http\Requests\API\UserUpdateRequest;
use App\Models\API\ApiUser;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return ApiUser::with('baseData')->get();
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = ApiUser::create($data);
        return response()->json($user, 201);
    }

    public function show(ApiUser $user)
    {
        return $user->load('baseData');
    }

    public function update(UserUpdateRequest $request, ApiUser $user)
    {
        $user->update($request->validated());
        return response()->json($user);
    }

    public function destroy(ApiUser $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
