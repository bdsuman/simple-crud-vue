<?php

namespace App\Services;

use App\Models\User;
use App\Traits\UploadAble;
use Illuminate\Http\Request;

class UpdateUserProfileService
{
    use UploadAble;

    public function updateProfile(Request $request): User
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $this->deleteFile($user->avatar, config('filesystems.default'));
            }
            $data['avatar'] = $this->uploadFile($request->file('avatar'), 'users/avatars', config('filesystems.default'));
        }

        $user->update($data);

        return $user->fresh();
    }
}
