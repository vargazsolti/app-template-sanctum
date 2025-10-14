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

        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => collect($data)->except('password'),
            ])
            ->log('user_created');

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

        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changed' => array_keys($data),
            ])
            ->log('user_updated');

        if ($passwordWasChanged) {
            $user->tokens()->delete();

            Log::info('User password changed; all Sanctum tokens revoked.', [
                'target_user_id' => $user->id,
                'performed_by_user_id' => $request->user()?->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            activity()
                ->performedOn($user)
                ->causedBy($request->user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('user_password_changed');
        }

        return response()->json($user);
    }

    public function destroy(ApiUser $user)
    {
        $user->delete();

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('user_deleted');

        return response()->noContent();
    }
}
