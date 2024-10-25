<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = $this->authUser();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'logo' => ''
        ];
        if(Storage::exists("/logos/$user->id")){
            $mimeType = Storage::mimeType("/logos/$user->id");
            $base64 = base64_encode(Storage::get("/logos/$user->id"));
            $data['logo'] = 'data:' . $mimeType . ';base64,' . $base64;
        }
        return response($data, 200);
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        if($request->hasFile('logo')){
            $file_name = $this->authUser()->id;
            Storage::putFileAs('/logos', $request->file('logo'), "$file_name");
        }
        $this->authUser()->update($validated);
        return response([
            'message' => 'Actualizado.'
        ], 200);
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
