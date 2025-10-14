<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserBaseDataStoreRequest;
use App\Http\Requests\API\UserBaseDataUpdateRequest;
use App\Models\UserBaseData;

class UserBaseDataController extends Controller
{
    public function index()
    {
        return UserBaseData::with('user')->get();
    }

    public function store(UserBaseDataStoreRequest $request)
    {
        $basedata = UserBaseData::create($request->validated());
        return response()->json($basedata, 201);
    }

    public function show(UserBaseData $user_basedatum)
    {
        return $user_basedatum->load('user');
    }

    public function update(UserBaseDataUpdateRequest $request, UserBaseData $user_basedatum)
    {
        $user_basedatum->update($request->validated());
        return response()->json($user_basedatum);
    }

    public function destroy(UserBaseData $user_basedatum)
    {
        $user_basedatum->delete();
        return response()->noContent();
    }
}
