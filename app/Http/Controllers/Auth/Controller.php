<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $remember_me = isset($validated['remember_me']);
        if($remember_me) unset($validated['remember_me']);
        if(
            Auth::attempt($validated, $remember_me)
        ){
            $request->session()->regenerate();
            return response(['message' => 'Autenticación exitosa'], 200);
        }
        return response(['message' => 'El usuario o la contraseña no son correctos.'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response([
            'message' => 'Sesión terminada.'
        ], 200);
    }
}
