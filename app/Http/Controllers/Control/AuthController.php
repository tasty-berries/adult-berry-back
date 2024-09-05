<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:8|max:24'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (Hash::check($data['password'], $user->password)) {
            return response()->json([
                'token' => $user->createToken('control')->plainTextToken
            ]);
        }

        abort(401);
    }
}
