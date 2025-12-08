<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $searchFields = ['email', 'full_name'];
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);
        
        $users = User::query()
            ->where('role', UserRoleEnum::USER)
            ->when($request->search, function ($q) use ($searchFields, $request) {
                $q->where(function ($query) use ($searchFields, $request) {
                    foreach ($searchFields as $field) {
                        $query->orWhere($field, 'ILIKE', "%{$request->search}%");
                    }
                });
            })
            ->paginate($perPage);

        return success_response(UserResource::collection($users), true, 'Users fetched successfully');
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function getUserRoles()
    {
        $roles = array_map(fn($role) => [
            'value' => $role->value,
            'label' => $role->label()
        ], UserRoleEnum::cases());
        return success_response($roles, false, 'User roles fetched successfully');
    }
}
