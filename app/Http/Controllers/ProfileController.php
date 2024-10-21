<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = $this->authUser();
        return [
            'name' => $user->name,
            'email' => $user->email
        ];
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $this->authUser()->update($validated);
        return response(['message' => 'Actualizado.'], 200);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();
        $this->authUser()->update([
            'password' => Hash::make($validated['new_password']),
        ]);
        return response(['message' => 'Actualizada.'], 200);
    }
}
