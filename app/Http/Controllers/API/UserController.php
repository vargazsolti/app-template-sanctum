<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserStoreRequest;
use App\Http\Requests\API\UserUpdateRequest;
use App\Models\API\ApiUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


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
        $data = $request->validated();

        $passwordWasChanged = false;

        if (array_key_exists('password', $data)) {
            if ($data['password'] === '' || $data['password'] === null) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
                $passwordWasChanged = true;
            }
        }

        $user->update($data);

        if ($passwordWasChanged) {
            // 1) Minden Sanctum token visszavonása az érintett usernél
            $user->tokens()->delete();

            // 2) Log bejegyzés
            Log::info('User password changed; all Sanctum tokens revoked.', [
                'target_user_id' => $user->id,
                'performed_by_user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return response()->json($user);
    }

    public function destroy(ApiUser $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
