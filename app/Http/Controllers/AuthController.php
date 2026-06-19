<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if(Auth::attempt($request->credentials, true)){
            $request->session()->regenerate();
            return redirect()->route('index');
        } else {
            throw ValidationException::withMessages(['password' => 'Неверный пароль']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }
}
